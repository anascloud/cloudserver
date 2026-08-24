<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleIdsAndCountryToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('password');
            $table->unsignedBigInteger('country')->nullable()->after('roles');
            $table->string('address')->nullable()->after('country');
            $table->string('avatar')->nullable()->after('address');
            $table->string('status')->nullable()->default('Active')->after('avatar');
            $table->string('lastName')->nullable()->after('firstName');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['roles', 'country', 'address', 'avatar', 'status', 'lastName']);
        });
    }
}
