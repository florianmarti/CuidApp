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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->after('email');
            $table->string('phone')->after('address');
            $table->enum('role', ['user', 'vigilador', 'admin'])->default('user')->after('phone');
            $table->boolean('is_confirmed')->default(false)->after('role');
            $table->string('confirmation_token')->nullable()->after('is_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'role', 'is_confirmed', 'confirmation_token']);
        });
    }
};
