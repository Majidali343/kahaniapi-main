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
        Schema::create('memberships', function (Blueprint $table) {
            $table->string('order_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('package_id');
            $table->string('membershipvalidity');
            $table->date('purchase_date');
            $table->string('status');
            $table->string('organization_request')->default('false');
            $table->string('admin_paid')->default('false');
            $table->string('coupon')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
