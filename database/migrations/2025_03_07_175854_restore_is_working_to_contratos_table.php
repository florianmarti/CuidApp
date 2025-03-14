<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            if (!Schema::hasColumn('contratos', 'is_working')) {
                $table->boolean('is_working')->default(false)->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('contratos', function (Blueprint $table) {
            if (Schema::hasColumn('contratos', 'is_working')) {
                $table->dropColumn('is_working');
            }
        });
    }
};
