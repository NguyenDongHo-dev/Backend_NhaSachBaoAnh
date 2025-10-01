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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete("cascade");
            $table->tinyInteger('rating')->unsigned();
            $table->string('comment')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->unique(['product_id', 'user_id']);
        });
    }


    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
