<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // DOSTAWA
            $table->string('delivery_method')->nullable();      // 'inpost' lub 'kurier'
            $table->string('delivery_point')->nullable();       // paczkomat (nazwa + adres)
            $table->string('delivery_address')->nullable();     // pełny adres dla kuriera
            $table->decimal('shipping_price', 8, 2)->default(0);

            // PŁATNOŚĆ
            $table->string('payment_method')->nullable();       // 'p24' lub 'transfer'
            $table->string('payment_status')->default('pending'); // 'pending', 'paid', 'cancelled'
            $table->string('payment_reference')->nullable();    // np. ID transakcji P24
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_method',
                'delivery_point',
                'delivery_address',
                'shipping_price',
                'payment_method',
                'payment_status',
                'payment_reference',
            ]);
        });
    }
};
