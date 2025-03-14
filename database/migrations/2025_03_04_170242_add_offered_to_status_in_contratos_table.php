<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferedToStatusInContratosTable extends Migration
{
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'offered', 'active', 'completed'])->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending')->change();
        });
    }
}
