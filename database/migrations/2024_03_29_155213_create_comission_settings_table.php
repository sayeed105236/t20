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
        Schema::create('comission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('refer_comission')->default(0);
            $table->string('level_comission_1')->default(0);
            $table->string('level_commission_2')->default(0);
            $table->string('level_comission_3')->default(0);
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
        Schema::dropIfExists('comission_settings');
    }
};
