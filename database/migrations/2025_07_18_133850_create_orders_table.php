<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->char("order_number", 10)->unique();
            $table->char('shipping_address', 200);
            $table->char('recipient_phone', 200)->fullText();
            $table->char('order_recipient_name', 200)->fullText();
            $table->char('delivery_method', 200);
            $table->integer("price_shipping");
            $table->integer("total_price");
            $table->bigInteger("total_all");
            $table->string("status", 100)->default("padding")->index();
            $table->text("notes")->nullable();
            $table->boolean("paid")->default(false)->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
