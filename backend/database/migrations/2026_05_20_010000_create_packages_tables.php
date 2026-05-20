<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title_fr');
            $table->string('title_en');
            $table->text('description_fr');
            $table->text('description_en');
            $table->string('city');
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            $table->string('currency', 3)->default('MAD');
            $table->string('image_url', 1000)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['city', 'is_active']);
        });

        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['hotel', 'restaurant', 'attraction', 'match', 'custom']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('custom_title')->nullable();
            $table->text('custom_description')->nullable();
            $table->unsignedTinyInteger('day_number')->default(1);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['package_id', 'day_number', 'sort_order']);
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('packages');
    }
};
