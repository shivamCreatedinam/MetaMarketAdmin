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
        Schema::create('user_login_activities', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string('last_login_ip', 50)->nullable();
            $table->string('last_login_device')->nullable();
            $table->string('last_login_browser')->nullable();
            $table->string('last_login_os')->nullable();
            $table->string('last_login_country', 100)->nullable();
            $table->string('last_login_region', 100)->nullable();
            $table->string('last_login_city', 100)->nullable();
            $table->string('last_login_timezone', 100)->nullable();
            $table->string('last_login_latitude', 100)->nullable();
            $table->string('last_login_longitude', 100)->nullable();
            $table->timestamp("last_login_at")->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_activities');
    }
};
