<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description_fr');
            $table->text('description_en');
            $table->string('city');
            $table->string('district')->nullable();
            $table->unsignedTinyInteger('stars');
            $table->decimal('price_min', 10, 2);
            $table->decimal('price_max', 10, 2);
            $table->string('currency', 3)->default('MAD');
            $table->string('website_url')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['city', 'stars']);
            $table->index(['price_min', 'price_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
