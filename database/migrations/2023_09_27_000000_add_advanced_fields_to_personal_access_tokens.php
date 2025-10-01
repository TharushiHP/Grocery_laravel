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
        // Only run if the table exists
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('personal_access_tokens', 'device_id')) {
                    $table->string('device_id')->nullable()->after('token');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'device_name')) {
                    $table->string('device_name')->nullable()->after('device_id');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('device_name');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'user_agent')) {
                    $table->string('user_agent')->nullable()->after('ip_address');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('last_used_at');
                }
                $table->index(['device_id', 'device_name']);
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('personal_access_tokens')) {
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
}