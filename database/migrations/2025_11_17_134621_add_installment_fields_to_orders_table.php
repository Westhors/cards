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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'installment'])
                ->default('cash')
                ->after('payment_status');

            $table->integer('installment_months')
                ->nullable()
                ->after('payment_type');

            $table->decimal('increase_rate', 5, 2)
                ->nullable()
                ->after('installment_months');

            $table->decimal('total_amount', 10, 2)
                ->nullable()
                ->after('increase_rate');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_type');
            $table->dropColumn('installment_months');
            $table->dropColumn('increase_rate');
        });
    }
};
