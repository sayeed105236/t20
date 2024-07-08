<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PurchaseVoucher;
use Carbon\Carbon;

class CheckVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:voucher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Voucher Check';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      //  $minings= UserMining::where('status',0)->get();



       
               
        $vouchers = PurchaseVoucher::where('status','Valid')->get();
        foreach($vouchers as $voucher)
        {
            $data = PurchaseVoucher::where('id',$voucher->id)->first();
            if($data->validity <= Carbon::now())
            {
                $data->status = 'Expired';
                $data->save();
                
            }
        }
         $vouchers = PurchaseVoucher::where('status','Expired')->get();
        foreach($vouchers as $voucher)
        {
            $data = PurchaseVoucher::where('id',$voucher->id)->first();
            if($data->validity >= Carbon::now())
            {
                $data->status = 'Valid';
                $data->save();
                
            }
        }

        $this->info('Successfully checked voucher.');

      //  $use=((($user['packages']['return_percentage']*$user['packages']['price'])/100)*$sponsor_bonus['royality_bonus']/100)*$income[$i]/100;
    
    }
}
