<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            if (!Schema::hasColumn('resumes', 'share_token')) {
                $table->string('share_token', 64)->nullable()->unique()->after('language');
            }
            if (!Schema::hasColumn('resumes', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('share_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'is_public']);
        });
    }
};
