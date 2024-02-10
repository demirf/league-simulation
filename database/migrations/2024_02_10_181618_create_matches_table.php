<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('home_team_id');
            $table->bigInteger('away_team_id');
            $table->integer('home_team_goals')->default(0);
            $table->integer('away_team_goals')->default(0);
            $table->integer('week');
            $table->bigInteger('tournament_id');
            $table->enum('status', ['complete', 'pending']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
