<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->unique()->after('plan_id')->nullable();
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->onDelete('set null');
            $table->integer('referral_count')->default(0)->after('referred_by');
        });
        
        // Populate existing users with codes
        \App\Models\User::all()->each(function ($user) {
            $user->update(['referral_code' => strtoupper(substr(md5($user->email), 0, 8))]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'referral_count']);
        });
    }
};
