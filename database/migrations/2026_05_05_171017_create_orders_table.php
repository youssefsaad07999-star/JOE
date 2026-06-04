<?php

use App\Models\Address;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdfor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdfor(Address::class)->nullable()->constrained()->nullOnDelete();
            $table->float('total_price');
            $table->timestamps();

            // user_id as a foreign key
            // product_id as a foreign key
            // order_amount
            // we need to make new table "orders_items" as orders and products got m to m relationship and will have attributes like quantity unit_price
            //

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
