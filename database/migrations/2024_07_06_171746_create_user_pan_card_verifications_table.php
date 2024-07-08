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
        Schema::create('user_pan_card_verifications', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("pan_no", 20);
            $table->text("pan_image")->nullable();
            $table->boolean("pan_verify_status")->default(0)->comment("0=>Pending, 1=>verified");
            $table->timestamp("pan_verified_at")->nullable()->default(null);
            $table->string("name_as_per_pan")->nullable();
            $table->text("pan_api_resp")->nullable();
            $table->text("remark")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pan_card_verifications');
    }
};
