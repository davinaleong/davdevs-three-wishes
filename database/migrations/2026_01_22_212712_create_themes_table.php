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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('year')->unique();
            $table->string('theme_title');
            $table->string('theme_tagline')->nullable();
            $table->string('theme_verse_reference')->default('Jer 29:11');
            $table->text('theme_verse_text');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->json('colors_json');
            $table->json('email_styles_json')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
