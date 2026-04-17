<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expert_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('file_url', 500);
            $table->string('original_name', 255)->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_documents');
    }
};
