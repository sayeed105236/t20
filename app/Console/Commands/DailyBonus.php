<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsdtWallet;
use App\Models\BizWallet;
use App\Models\User;
use App\Models\PackageSetting;
use App\Models\PurchasePackage;
use Carbon\Carbon;
use DateTime;
use function Sodium\add;
use Auth;
use App\Models\A2IWallet;
use App\Models\UserMining;
class DailyBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily package bonus';

    
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
     {
        //return Command::SUCCESS;


        $minings= UserMining::where('status',0)->get();



        foreach ($minings as $row) {
                //$purchase= PurchasePackage::where('status',0)->where('package_id',$row->package_id)->first();
                $date1 = Carbon::parse($row['created_at']);
                $date2 = Carbon::now();
                $days = $date2->diffInHours($date1);
               // dd($days);
                $package= PackageSetting::where('id',$row->package_id)->first();
               // $sponsor_id= User::where('id',$row->user_id)->first();
               //$purchase= PurchasePackage::where('status',0)->where('package_id',$row->package_id)->first();

                if ($days > 24 ){
                     $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $txn_id = substr(str_shuffle($chars), 0, 11);
                    $bonus= new BizWallet();
                    $bonus->user_id= $row->user_id;

                    $bonus->amount= $package->daily_token;
                    $bonus->method= 'Daily Package Bonus';
                    $bonus->type= 'Debit';
                    $bonus->status = 'approved';
                    $bonus->txn_id = $txn_id;
                    $bonus->description= $package->daily_token . ' Daily Bonus for purchasing '. ' ' . $package->package_name;
                    $bonus->save();
                    $update_row= UserMining::where('id',$row->id)->first();
                    $update_row->status = 1;
                    $update_row->save();
                    $txn_id_2 = substr(str_shuffle($chars), 0, 11);
                    $a2i_bonus = new A2IWallet();
                    $a2i_bonus->user_id = $row->user_id;;
                    $a2i_bonus->type = 'Debit';
                    $a2i_bonus->method = 'A2I Bonus';
                  //  $a2i_bonus->received_from = Auth::id();
                    $a2i_bonus->status = 'approved';
                    $a2i_bonus->amount = $package->a2i_token;
                    $a2i_bonus->txn_id = $txn_id_2;
                   // $bonus->wallet_id = $validatedData['wallet_id'];
                    $a2i_bonus->description= $package->a2i_token.' A2I token received for purchasing package';
                    $a2i_bonus->save();


            
        }
        $purchases = PurchasePackage::all();
        foreach($purchases as $purchase)
        {
            $data = PurchasePackage::where('id',$purchase->id)->first();
            $pckg= PackageSetting::where('id',$data->package_id)->first();
            if(Carbon::now() >= (($data->created_at)->addDays($pckg->duration)))
            {
                $data->status = 1;
                $data->save();
            }
        }

        $this->info('Successfully added daily bonus.');

      //  $use=((($user['packages']['return_percentage']*$user['packages']['price'])/100)*$sponsor_bonus['royality_bonus']/100)*$income[$i]/100;
    }
  }
}
