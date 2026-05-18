<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description_fr');
            $table->text('description_en');
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('category');
            $table->decimal('entry_price', 10, 2)->nullable();
            $table->string('opening_hours')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['city', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
