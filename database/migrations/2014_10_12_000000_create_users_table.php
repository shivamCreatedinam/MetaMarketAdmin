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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid();
            $table->string('name');
            $table->string('username')->nullable();
            $table->text('profile_image')->nullable();
            $table->string('email', 100)->unique();
            $table->string('mobile_no', 20)->unique();
            $table->enum('role', ["user", "superadmin"])->default("user")->comment("user, superadmin");
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('temp_token')->nullable();
            $table->string('password');
            $table->boolean('aadhar_verified')->default(0);
            $table->boolean('pan_verified')->default(0);
            $table->boolean('bank_verified')->default(0);
            $table->boolean('vpa_verified')->default(0);
            $table->boolean('kyc_verified')->default(0);
            $table->string('google2fa_secret')->nullable();
            $table->enum('google2fa_enable', ["yes", "no"])->default("no");
            $table->timestamp('google2fa_enable_at')->nullable()->default(null);
            $table->enum('user_status',["active","block","ban"])->nullable()->default("null");

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
