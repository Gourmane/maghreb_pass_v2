<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('email');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->text('map_url')->nullable()->after('longitude');
            $table->boolean('is_featured')->default(false)->after('map_url');
            $table->decimal('rating', 2, 1)->nullable()->after('is_featured');
            $table->json('amenities')->nullable()->after('rating');
            $table->text('image_url')->nullable()->after('amenities');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('whatsapp');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->text('map_url')->nullable()->after('longitude');
            $table->boolean('is_featured')->default(false)->after('map_url');
            $table->decimal('rating', 2, 1)->nullable()->after('is_featured');
            $table->string('opening_hours')->nullable()->after('rating');
            $table->text('image_url')->nullable()->after('opening_hours');
        });

        Schema::table('attractions', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('opening_hours');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->text('map_url')->nullable()->after('longitude');
            $table->boolean('is_featured')->default(false)->after('map_url');
            $table->decimal('rating', 2, 1)->nullable()->after('is_featured');
            $table->unsignedInteger('recommended_duration_minutes')->nullable()->after('rating');
            $table->text('image_url')->nullable()->after('recommended_duration_minutes');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->decimal('stadium_latitude', 10, 7)->nullable()->after('stadium');
            $table->decimal('stadium_longitude', 10, 7)->nullable()->after('stadium_latitude');
            $table->text('map_url')->nullable()->after('stadium_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'map_url',
                'is_featured',
                'rating',
                'amenities',
                'image_url',
            ]);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'map_url',
                'is_featured',
                'rating',
                'opening_hours',
                'image_url',
            ]);
        });

        Schema::table('attractions', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'map_url',
                'is_featured',
                'rating',
                'recommended_duration_minutes',
                'image_url',
            ]);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'stadium_latitude',
                'stadium_longitude',
                'map_url',
            ]);
        });
    }
};
