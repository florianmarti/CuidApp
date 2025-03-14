<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionCountAndIsSuspendedToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('rejection_count')->default(0)->after('email');
            $table->boolean('is_suspended')->default(false)->after('rejection_count');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rejection_count');
            $table->dropColumn('is_suspended');
        });
    }
}
