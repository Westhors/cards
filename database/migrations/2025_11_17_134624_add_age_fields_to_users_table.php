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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->string('id_image')->nullable();
            $table->string('bank_statement_image')->nullable();
            $table->string('invoice_image')->nullable();
            $table->boolean('is_refused')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->string('id_image')->nullable();
            $table->string('bank_statement_image')->nullable();
            $table->string('invoice_image')->nullable();
            $table->boolean('active')->default(false);
     });
    }
};
