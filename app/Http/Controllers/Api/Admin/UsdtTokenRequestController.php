<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsdtWallet;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\BizWallet;
class UsdtTokenRequestController extends Controller
{
    public function index()
    {
        $admin_requests= UsdtWallet::select('usdt_wallets.*','users.name','users.email','admin_wallets.wallet_name','admin_wallets.wallet_no','admin_wallets.network')
        ->join('users','users.id','usdt_wallets.user_id')
          ->join('admin_wallets','admin_wallets.id','usdt_wallets.wallet_id')
        ->where('method','Deposit')->get();
        return response()->json([$admin_requests]);
    }
    
    public function Approve(Request $request)
    {
        $deposit_request= UsdtWallet::where('id',$request->id)->first();
       // dd($deposit_request);
        $user= User::where('id',$deposit_request->user_id)->first();
        if($request->status == 1)
        {
            
            $deposit_request->status = 'approved'; 
            $deposit_request->description= $request->amount . ' Deposit requested from '.$user->name .'('.$user->email.') accepted by Admin';
           
        }else 
        {
             $deposit_request->status = 'rejected'; 
             $deposit_request->description= $request->amount . ' Deposit requested from '.$user->name .'('.$user->email.') rejected by Admin';
            
        }
         //dd("jsaskjd");
        $deposit_request->save();
        
        
         return response()->json([
             'deposit'=> $deposit_request,
             'success'=> 200]);
        
    }
     public function WithdrawRequest()
    {
        $withdraw_requests= Withdraw::select('withdraws.*','users.name','users.email','admin_wallets.wallet_name','admin_wallets.network')
        ->join('users','users.id','withdraws.user_id')
          ->join('admin_wallets','admin_wallets.id','withdraws.main_wallet_id')
            ->get();
        return response()->json([$withdraw_requests]);
    }
    
    public function WithdrawApprove(Request $request)
    {
        $withdraw_request= Withdraw::where('id',$request->id)->first();
       // dd($deposit_request);
        $user= User::where('id',$withdraw_request->user_id)->first();
        if($request->status == 1)
        {
            
            $withdraw_request->status = 'approved'; 
            $withdraw_request->description= $request->amount . ' Withdrawal requested from '.$user->name .'('.$user->email.') accepted by Admin';
           
        }elseif($request->status == 0) 
        {
             $withdraw_request->status = 'rejected'; 
             $withdraw_request->description= $request->amount . ' Withdrawal requested from '.$user->name .'('.$user->email.') rejected by Admin';
                $deduct = new BizWallet();
                $deduct->user_id = $withdraw_request->user_id;
                $deduct->type = 'Debit';
                $deduct->method = 'Refund Withdrawal';
                $deduct->status = 'approved';
                $deduct->amount = $withdraw_request->amount + $withdraw_request->charge;
                $deduct->txn_id = $withdraw_request->txn_id;
               // $deposit->wallet_id = $validatedData['wallet_id'];
                $deduct->description= $withdraw_request->amount . ' withdrawal request rejected from admin and your balance has been refunded to your wallet ';
                $deduct->save();
            
        }
         //dd("jsaskjd");
        $withdraw_request->save();
        
        
         return response()->json([
             'withdraw'=> $withdraw_request,
             'success'=> 200]);
        
    }
    
}
