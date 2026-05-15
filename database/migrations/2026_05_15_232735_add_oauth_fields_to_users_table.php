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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('oauth_provider')->nullable()->after('website_url');
            $table->string('oauth_provider_id')->nullable()->after('oauth_provider');
            $table->text('app_authentication_secret')->nullable()->after('two_factor_secret');
            $table->text('app_authentication_recovery_codes')->nullable()->after('app_authentication_secret');

            $table->unique(['oauth_provider', 'oauth_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['oauth_provider', 'oauth_provider_id']);
            $table->dropColumn([
                'oauth_provider',
                'oauth_provider_id',
                'app_authentication_secret',
                'app_authentication_recovery_codes',
            ]);
        });
    }
};
