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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('MugiewBlog');
            $table->string('tagline')->default('Engineering notes untuk developer yang mengirim fitur ke produksi.');
            $table->string('site_url')->default('https://mugiewblog.test');
            $table->text('site_description');
            $table->string('default_og_image')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('sitemap_enabled')->default(true);
            $table->boolean('rss_enabled')->default(true);
            $table->boolean('newsletter_enabled')->default(true);
            $table->unsignedSmallInteger('articles_per_page')->default(11);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
