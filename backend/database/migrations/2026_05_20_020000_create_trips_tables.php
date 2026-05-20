<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date']);
        });

        Schema::create('trip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['hotel', 'restaurant', 'attraction', 'match', 'package', 'custom']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('custom_title')->nullable();
            $table->text('custom_description')->nullable();
            $table->unsignedTinyInteger('day_number')->default(1);
            $table->time('start_time')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'day_number', 'sort_order']);
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_items');
        Schema::dropIfExists('trips');
    }
};
