<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Eliminar user_id si existe
            if (Schema::hasColumn('notifications', 'user_id')) {
                // Migrar datos existentes de user_id a notifiable_id
                \DB::statement('UPDATE notifications SET notifiable_id = user_id, notifiable_type = ?', ['App\\Models\\User']);
                $table->dropColumn('user_id');
            }
            // Asegurar que las columnas polimórficas existan
            $table->string('notifiable_type')->default('App\\Models\\User')->after('id');
            $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');
            $table->json('data')->nullable()->after('notifiable_id');
            $table->boolean('read')->default(false)->after('data');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            \DB::statement('UPDATE notifications SET user_id = notifiable_id');
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
    }
};
