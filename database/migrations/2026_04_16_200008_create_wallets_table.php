<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->decimal('total_withdrawn', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('amount', 10, 2);
            $table->string('description', 255)->nullable();
            $table->string('reference', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
