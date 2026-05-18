<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description_fr');
            $table->text('description_en');
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('cuisine_type');
            $table->enum('price_range', ['budget', 'moyen', 'gastronomique']);
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['city', 'cuisine_type', 'price_range']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
