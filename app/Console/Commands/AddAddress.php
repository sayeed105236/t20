<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BlockgumService;
use App\Models\UsdtWallet;


class AddAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Address Add';

    /**
     * Execute the console command.
     *
     * @return int
     */
     protected $blockgumService;

    public function __construct(BlockgumService $blockgumService)
    {
        parent::__construct();
        $this->blockgumService = $blockgumService;
    }
    public function handle()
    {
        //return Command::SUCCESS;


        $users= User::where('is_verified',1)->where('deposit_address',null)->get();
        foreach($users as $user)
        {
            $address= User::where('id',$user->id)->first();
            //$uid = $address->id();
            $uid= $address->id;
            $response = $this->blockgumService->createAddress($uid);
            $address->deposit_address = $response['address'];
            $address->save();
       // $response = $this->blockgumService->createAddress($uid);
        }
        $all_users = User::all();
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $txn_id_3 = substr(str_shuffle($chars), 0, 11);
        foreach($all_users as $data)
        {
            $sponsor_count= User::where('sponsor',$data->referral_code)->where('is_verified',1)->count();
            $data= User::where('id',$data->id)->first();
            $data->free_direct_refer = $sponsor_count;
            $data->save();
            $leadership_bonus_count= UsdtWallet::where('user_id',$data->id)->where('method','Leadership Bonus')->count();
          //  dd($leadership_bonus_count);
           
            
            if($data->free_direct_refer >= 25 && $leadership_bonus_count == 0)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $data->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
           // $bonus->received_from = Auth::id();
            $bonus->amount = 10;
            $bonus->txn_id = $txn_id_3;
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '10 Leadership bonus';
            $bonus->save();
                
            }
            
            elseif($data->free_direct_refer >= 50 && $leadership_bonus_count == 1)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $data->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
           // $bonus->received_from = Auth::id();
            $bonus->amount = 25;
            //$bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
            $bonus->txn_id = $txn_id_3;
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '25 Leadership bonus';
            $bonus->save();
                
            }
            elseif($data->free_direct_refer >= 100 && $leadership_bonus_count == 2)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $data->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
           // $bonus->received_from = Auth::id();
            $bonus->amount = 60;
           // $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->txn_id = $txn_id_3;
            $bonus->description= '60 Leadership bonus';
            $bonus->save();
                
            }
             elseif($data->free_direct_refer >= 500 && $leadership_bonus_count == 3)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $data->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
           // $bonus->received_from = Auth::id();
            $bonus->amount = 350;
           // $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
             $bonus->txn_id = $txn_id_3;
            $bonus->description= '350 Leadership bonus';
            $bonus->save();
                
            }
             elseif($data->free_direct_refer >= 1000 && $leadership_bonus_count == 4)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $data->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
           // $bonus->received_from = Auth::id();
            $bonus->amount = 750;
           // $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
            $bonus->txn_id = $txn_id_3;
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '750 Leadership bonus';
            $bonus->save();
                
            }
            
            
            //if($spon)
        
        }
         $this->info('Successfully added address.');




       
    }
}
