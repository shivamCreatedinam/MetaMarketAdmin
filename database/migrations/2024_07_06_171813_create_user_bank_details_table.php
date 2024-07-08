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
        Schema::create('user_bank_details', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("bank_name",100)->nullable();
            $table->text("bank_image")->nullable();
            $table->string("account_number",50)->nullable();
            $table->string("ifsc_code",50)->nullable();
            $table->string("account_holder_name",100)->nullable();
            $table->boolean("nameMatch")->default(0)->comment("0=>Pending, 1=>verified");
            $table->boolean("mobileMatch")->default(0)->comment("0=>Pending, 1=>verified");
            $table->boolean("is_default")->default(0)->comment("0=>Pending, 1=>verified");
            $table->timestamp("bank_verified_at")->nullable()->default(null);
            $table->text("bank_response")->nullable();
            $table->string("ip_address",20)->nullable();
            $table->boolean("bank_verify_status")->default(0)->comment("0=>Pending, 1=>verified");
            $table->string("vpa",100)->nullable();
            $table->boolean("vpa_status")->default(0)->comment("0=>Pending, 1=>verified");
            $table->text("vpa_response")->nullable();
            $table->string("vpa_name",100)->nullable();
            $table->timestamp("vpa_verified_at")->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_details');
    }
};
