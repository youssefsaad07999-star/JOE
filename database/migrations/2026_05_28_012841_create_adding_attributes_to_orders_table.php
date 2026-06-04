<?php

use App\Models\ShippingMethod;
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
            $table->string('status')->default('pending')->after('total_price');
            $table->string('shipping_first_name')->nullable()->after('status');
            $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
            $table->string('shipping_address')->nullable()->after('shipping_last_name');
            $table->string('shipping_address2')->nullable()->after('shipping_address');
            $table->string('shipping_city')->nullable()->after('shipping_address2');
            $table->string('shipping_postal_code')->nullable()->after('shipping_city');
            $table->string('shipping_country')->nullable()->after('shipping_postal_code');
            $table->string('shipping_phone')->nullable()->after('shipping_country');
            $table->foreignIdFor(ShippingMethod::class)->nullable()->constrained()->nullOnDelete();
            $table->string('shipping_method')->nullable()->after('shipping_phone');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adding_attributes_to_orders');
    }
};
