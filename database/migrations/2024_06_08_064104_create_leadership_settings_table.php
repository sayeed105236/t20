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
        Schema::create('leadership_settings', function (Blueprint $table) {
            $table->id();
            $table->float('free_direct_refer_1_qty')->default(0);
            $table->float('free_direct_refer_1_amount')->default(0);
            $table->float('free_direct_refer_2_qty')->default(0);
            $table->float('free_direct_refer_2_amount')->default(0);
            $table->float('free_direct_refer_3_qty')->default(0);
            $table->float('free_direct_refer_3_amount')->default(0);
            $table->float('free_direct_refer_4_qty')->default(0);
            $table->float('free_direct_refer_4_amount')->default(0);
            $table->float('free_direct_refer_5_qty')->default(0);
            $table->float('free_direct_refer_5_amount')->default(0);
            
            $table->float('free_team_member_1_qty')->default(0);
            $table->float('free_team_member_1_amount')->default(0);
            $table->float('free_team_member_2_qty')->default(0);
            $table->float('free_team_member_2_amount')->default(0);
            $table->float('free_team_member_3_qty')->default(0);
            $table->float('free_team_member_3_amount')->default(0);
            $table->float('free_team_member_4_qty')->default(0);
            $table->float('free_team_member_4_amount')->default(0);
            $table->float('free_team_member_5_qty')->default(0);
            $table->float('free_team_member_5_amount')->default(0);
            
            $table->float('paid_direct_refer_1_qty')->default(0);
            $table->float('paid_direct_refer_1_amount')->default(0);
            $table->float('paid_direct_refer_2_qty')->default(0);
            $table->float('paid_direct_refer_2_amount')->default(0);
            $table->float('paid_direct_refer_3_qty')->default(0);
            $table->float('paid_direct_refer_3_amount')->default(0);
            $table->float('paid_direct_refer_4_qty')->default(0);
            $table->float('paid_direct_refer_4_amount')->default(0);
            $table->float('paid_direct_refer_5_qty')->default(0);
            $table->float('paid_direct_refer_5_amount')->default(0);
            
            $table->float('paid_team_member_1_qty')->default(0);
            $table->float('paid_team_member_1_amount')->default(0);
            $table->float('paid_team_member_2_qty')->default(0);
            $table->float('paid_team_member_2_amount')->default(0);
            $table->float('paid_team_member_3_qty')->default(0);
            $table->float('paid_team_member_3_amount')->default(0);
            $table->float('paid_team_member_4_qty')->default(0);
            $table->float('paid_team_member_4_amount')->default(0);
            $table->float('paid_team_member_5_qty')->default(0);
            $table->float('paid_team_member_5_amount')->default(0);
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
        Schema::dropIfExists('leadership_settings');
    }
};
