<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('job_title');
            $table->string('location')->nullable();
            $table->string('job_url')->nullable();
            $table->string('status')->default('saved');
            // status options: saved | applied | interview | offer | rejected | withdrawn
            $table->date('applied_at')->nullable();
            $table->date('interview_at')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency')->default('USD');
            $table->text('notes')->nullable();
            $table->integer('excitement')->default(3); // 1-5 star rating
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
