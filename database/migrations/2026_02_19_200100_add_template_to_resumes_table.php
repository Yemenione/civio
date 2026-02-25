<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add `template` column to resumes table as an alias for the existing `theme` column.
 * The new CV template system uses `template` (slug) to identify which Vue component to render.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            if (!Schema::hasColumn('resumes', 'template')) {
                $table->string('template')->default('classic')->after('theme');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            if (Schema::hasColumn('resumes', 'template')) {
                $table->dropColumn('template');
            }
        });
    }
};
