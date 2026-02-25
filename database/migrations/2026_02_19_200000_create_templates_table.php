<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. "Classic"
            $table->string('slug')->unique();                // e.g. "classic"
            $table->string('component');                     // Vue component name e.g. "ClassicTemplate"
            $table->text('description')->nullable();
            $table->string('preview_image')->nullable();     // path to preview image
            $table->string('category')->default('standard'); // standard | rtl | creative
            $table->boolean('is_premium')->default(false);   // true = Pro only
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('supported_languages')->nullable(); // ["en","ar","fr"] etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
