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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('promotional_offer_image_one')->nullable();
            $table->string('title_one')->nullable();
            $table->string('promotional_offer_image_two')->nullable();
            $table->string('title_two')->nullable();
            $table->string('promotional_offer_image_three')->nullable();
            $table->string('title_three')->nullable();
             $table->string('promotional_offer_image_four')->nullable();
            $table->string('title_four')->nullable();
             $table->string('promotional_offer_image_five')->nullable();
            $table->string('title_five')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
