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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('github_url')->nullable()->after('bio');
            $table->string('twitter_url')->nullable()->after('github_url');
            $table->string('website_url')->nullable()->after('twitter_url');
            $table->string('role')->default('user')->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('role');
            $table->boolean('two_factor_enabled')->default(false)->after('is_active');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->softDeletes();

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropUnique(['username']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'username',
                'avatar',
                'bio',
                'github_url',
                'twitter_url',
                'website_url',
                'role',
                'is_active',
                'two_factor_enabled',
                'two_factor_secret',
            ]);
        });
    }
};
