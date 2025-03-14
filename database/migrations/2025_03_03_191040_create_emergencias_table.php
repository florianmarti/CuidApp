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
        Schema::create('emergencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vigilador_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('timestamp');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergencias');
    }
};
