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
        Schema::create('user_aadhar_verifications', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("aadhar_no", 12)->unique();
            $table->text("aadhar_photo_front")->nullable();
            $table->text("aadhar_photo_back")->nullable();
            $table->string("request_id")->nullable();
            $table->boolean("verify_status")->default(0)->comment("0=>Pending, 1=>verified");
            $table->timestamp("verified_at")->nullable()->default(null);
            $table->string("name_as_per_aadhar")->nullable();
            $table->text("api_response")->nullable();
            $table->text("remark")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_aadhar_verifications');
    }
};
