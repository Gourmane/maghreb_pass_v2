<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('team_home');
            $table->string('team_home_code', 3)->nullable();
            $table->string('team_home_flag_url')->nullable();
            $table->string('team_away');
            $table->string('team_away_code', 3)->nullable();
            $table->string('team_away_flag_url')->nullable();
            $table->unsignedTinyInteger('score_home')->nullable();
            $table->unsignedTinyInteger('score_away')->nullable();
            $table->date('match_date');
            $table->time('match_time');
            $table->string('stadium');
            $table->string('city');
            $table->string('group_name')->nullable();
            $table->enum('phase', ['group', 'round_of_16', 'quarter', 'semi', 'final'])->default('group');
            $table->enum('status', ['upcoming', 'live', 'finished'])->default('upcoming');
            $table->timestamps();

            $table->index(['city', 'phase', 'status']);
            $table->index('match_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
