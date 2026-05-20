<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hotel_reservations MODIFY status ENUM('pending', 'approved', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE restaurant_reservations MODIFY status ENUM('pending', 'approved', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('hotel_reservations', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->unique()->after('paid_at');
            $table->index(['payment_status', 'status']);
        });

        Schema::table('restaurant_reservations', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->unique()->after('paid_at');
            $table->index(['payment_status', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_reservations', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'status']);
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_status', 'paid_at', 'payment_reference']);
        });

        Schema::table('hotel_reservations', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'status']);
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_status', 'paid_at', 'payment_reference']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hotel_reservations MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE restaurant_reservations MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
