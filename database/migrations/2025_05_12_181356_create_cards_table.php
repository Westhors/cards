<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('slug')->nullable();
            $table->string('type_silicone')->nullable();
            $table->string('hardness')->nullable();
            $table->string('bio')->nullable();
            $table->string('time_in_ear')->nullable();
            $table->string('end_curing')->nullable();
            $table->string('viscosity')->nullable();
            $table->string('color')->nullable();
            $table->string('packaging')->nullable();
            $table->string('item_number')->nullable();
            $table->string('mix_gun')->nullable();
            $table->string('mix_canules')->nullable();

            $table->text('description')->nullable();
            $table->text('short_description')->nullable();

            $table->decimal('old_price', 10, 2)->nullable();
            $table->string('discount')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('link_video')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();

            $table->integer('quantity')->nullable();

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->unsignedBigInteger('admin_id');

            $table->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();

            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
