<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BlockgumService;
use App\Models\UsdtWallet;
use DB;


class CheckDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Deposit';

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


        $users= User::where('is_verified',1)->where('deposit_address','!=',null)->get();
        foreach($users as $user)
        {
            $address= User::where('id',$user->id)->first();
            //$uid = $address->id();
           // $uid= $address->id;
             $response = $this->blockgumService->getLatestDeposits();
         //  dd($response);
            //$address->deposit_address = $response['address'];
           // $address->save();
       // $response = $this->blockgumService->createAddress($uid);
       if (is_array($response)) {
    foreach ($response as $deposit) {
        // Check if each deposit contains the keys you are trying to access
        if (isset($deposit['hash'], $deposit['uid'], $deposit['value'])) {
            
        $check_deposit = $this->blockgumService->traceDeposit($deposit['hash']);
        $hash_check = UsdtWallet::where('user_id',$deposit['uid'])->where('txn_id',$deposit['hash'])->first();
        if($check_deposit['status'] == 1 && $check_deposit['message'] == 'Deposit exists' && $hash_check == null)
        {
           // $user_id = User::where('email',$user)->first();
        //dd($deposit['hash']);
        $amount = $deposit['value']/1000000000000000000;
        $txn_id = $deposit['hash'];
        $uid = $deposit['uid'];
      // DB::Begintransaction();
      $deposit = new UsdtWallet();
    $deposit->user_id = $uid;
    $deposit->type = 'Debit';
    $deposit->method = 'Deposit';
    $deposit->status = 'approved';
    $deposit->amount = $amount;
    $deposit->txn_id = $txn_id;
    //$deposit->wallet_id = $validatedData['wallet_id'];
    $deposit->description= $amount . ' Deposited successfully by payment gateway';;
   // $deposit->save();
  //  dd($deposit);
        }
            //dd($check_deposit['status'],$check_deposit['message']);
        } else {
            // Handle the case where a deposit does not contain the expected keys
           // dd($deposit);
        }
    }
} else {
    // Handle the case where the response is not an array as expected
   // dd($response);
}
        }
         $this->info('Successfully checked.');




       
    }
}
