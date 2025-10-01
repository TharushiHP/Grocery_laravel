<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvancedFieldsToPersonalAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('device_id')->nullable()->after('token');
            $table->string('device_name')->nullable()->after('device_id');
            $table->string('ip_address', 45)->nullable()->after('device_name');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->timestamp('expires_at')->nullable()->after('last_used_at');
            $table->index(['device_id', 'device_name']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn([
                'device_id',
                'device_name',
                'ip_address',
                'user_agent',
                'expires_at'
            ]);
        });
    }
}