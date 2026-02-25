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
        Schema::create('plans', function (Blueprint $group) {
            $group->id();
            $group->string('name');
            $group->string('slug')->unique();
            $group->string('description')->nullable();
            $group->decimal('price_monthly', 8, 2)->default(0);
            $group->decimal('price_yearly', 8, 2)->default(0);
            $group->string('stripe_price_id_monthly')->nullable();
            $group->string('stripe_price_id_yearly')->nullable();
            $group->json('features')->nullable(); // e.g., ['ats_check' => true, 'resumes_limit' => 5]
            $group->boolean('is_active')->default(true);
            $group->boolean('is_popular')->default(false);
            $group->integer('sort_order')->default(0);
            $group->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
