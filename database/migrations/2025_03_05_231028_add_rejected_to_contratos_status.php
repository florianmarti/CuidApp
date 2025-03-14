<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'offered', 'active', 'rejected'])->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'offered', 'active'])->default('pending')->change();
        });
    }
};
