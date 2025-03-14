<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Añadir solo lo que falta
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('profile_picture');
            }
            // Renombrar 'phone' a 'phone_number' si existe, o añadirlo si no
            if (Schema::hasColumn('users', 'phone') && !Schema::hasColumn('users', 'phone_number')) {
                $table->renameColumn('phone', 'phone_number');
            } elseif (!Schema::hasColumn('users', 'phone') && !Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_picture')) {
                $table->dropColumn('profile_picture');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->renameColumn('phone_number', 'phone');
            } elseif (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
