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
        Schema::create('country_shippings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();             // اسم البلد
            $table->string('iso_code')->nullable()->index(); // كود iso (اختياري)
            $table->decimal('shipping_price', 10, 2)->default(0); // سعر الشحن
            $table->string('currency', 3)->default('EUR'); // العملة، افتراضاً EUR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_shippings');
    }
};
