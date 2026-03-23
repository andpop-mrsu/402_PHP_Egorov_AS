<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->integer('step_number');
            $table->string('answered_at');
            $table->text('progression_with_gap');
            $table->text('progression_full');
            $table->integer('missing_number');
            $table->string('user_answer');
            $table->boolean('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
