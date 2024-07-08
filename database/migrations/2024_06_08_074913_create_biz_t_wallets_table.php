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
        Schema::create('biz_t_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('received_by')->nullable();
            $table->integer('received_from')->nullable();
            $table->string('amount');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->string('txn_id');
            $table->string('method');
            $table->integer('wallet_id')->nullable();
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biz_t_wallets');
    }
};
