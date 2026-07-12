<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_id')
                ->nullable()
                ->constrained('deliveries')
                ->onDelete('set null');

            $table->string('delivery_status')
                ->default('pending'); // pending - in_progress - delivered - cancelled
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_id']);
            $table->dropColumn(['delivery_id', 'delivery_status']);
        });
    }
};
