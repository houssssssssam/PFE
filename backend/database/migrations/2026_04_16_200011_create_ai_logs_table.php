<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->string('workflow', 100);
            $table->text('prompt')->nullable();
            $table->text('response')->nullable();
            $table->string('model', 50)->nullable();
            $table->decimal('confidence', 3, 2)->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->boolean('escalated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
