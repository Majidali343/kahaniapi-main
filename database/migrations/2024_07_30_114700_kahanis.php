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
       Schema::create('kahanis', function (Blueprint $table) {
           $table->bigIncrements('kahani_id');
           $table->string('title');
           $table->text('description');
           $table->string('Duration');
           $table->string('free');
           $table->string('views');
           $table->string('audio');
           $table->string('video')->nullable();
           $table->string('image');
           $table->string('pg');
           $table->string('thumbnail')->nullable();
           $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Kahanis');
    }
};
