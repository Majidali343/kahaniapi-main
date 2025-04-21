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
        Schema::create('Coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code');
            $table->string('admin_id')->nullable();
            $table->string('discount_percentage');
            $table->string('organization_stake');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Coupons');
    }
};
