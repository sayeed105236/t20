<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BizWallet;
use App\Models\UserWallet;
use Auth;
use App\Models\Withdraw;
use App\Models\WithdrawSetting;
use App\Models\UserMining;
use Carbon\Carbon;
use App\Models\ComissionSetting;
use App\Models\PackageSetting;
use App\Models\PurchasePackage;
use App\Models\ConvertSetting;
use App\Models\BizTWallet;
class BizWalletController extends Controller
{
    public function index()
    {
        $wallet= BizWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
         return response()->json([
             'biz_balance'=> $wallet]);
    }
    public function WithdrawMoney(Request $request)
    {
         $rules = [
        'amount' => 'required',
        'wallet_address' => 'required',
        'main_wallet_id' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);
     $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $txn_id = substr(str_shuffle($chars), 0, 11);
    $withdraw_setting= WithdrawSetting::first();
    $balance= BizWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
    if($withdraw_setting->status == 0)
    {
        $charge= 0;
    }else 
    {
        $charge= $withdraw_setting->withdrwal_charge;
    }
    $amount= $request->amount+ $request->amount*($charge/100);
    
    if($balance < $amount)
    {
         return response()->json(['balance' => $balance,
            'failed' => 400,
            'message'=> 'Insufficient Balance']);
    }
    if($balance < $withdraw_setting->min_withdraw)
    {
         return response()->json(['balance' => $balance,'withdraw_settings'=>$withdraw_setting,
            'failed' => 400,
            'message'=> 'Amount should be higher or equal to '.$withdraw_setting->min_withdraw]);
    }
     if($balance > $withdraw_setting->max_withdraw)
    {
         return response()->json(['balance' => $balance,'withdraw_settings'=>$withdraw_setting,
            'failed' => 400,
            'message'=> 'Amount should be less or equal to '.$withdraw_setting->max_withdraw]);
    }
    // Create a new PakageSetting instance and save it
    $withdraw= new Withdraw();
    $withdraw->user_id= Auth::id();
    $withdraw->main_wallet_id = $validatedData['main_wallet_id'];
    $withdraw->wallet_address = $validatedData['wallet_address'];
    $withdraw->amount = $validatedData['amount'];
    $withdraw->charge = $charge;
    $withdraw->txn_id = $txn_id;
    $withdraw->description= $request->amount . ' Withdrawal requested from '.Auth::user()->name .'('.Auth::user()->email.')';
    $withdraw->save();
    $deduct = new BizWallet();
    $deduct->user_id = Auth::id();
    $deduct->type = 'Credit';
    $deduct->method = 'Withdraw';
    $deduct->status = 'approved';
    $deduct->amount = -($amount);
    $deduct->txn_id = $txn_id;
   // $deposit->wallet_id = $validatedData['wallet_id'];
    $deduct->description= $request->amount . ' Withdrawal requested from '.Auth::user()->name .'('.Auth::user()->email.')';
    $deduct->save();
     
    // Return the saved data as JSON response
    return response()->json(['deduct' => $deduct,
    'success' => 200]);
    }
    public function FreeMiningCheck()
    {
        $data= BizWallet::where('method','Daily Mining')->where('user_id',Auth::id())->orderBy('id','desc')->first();
        if($data != null)
        {
            $last_mining = ($data->created_at)->addHours(24);
            if(Carbon::now() > $last_mining)
            {
                 return response()->json(['message' => 'User can claim token',
            'success' => 200]);
                
            }else 
            {
                return response()->json(['message' => 'User cannot claim token',
                 'start_time'=> $data->created_at,
            'end_time'=> $last_mining,
            'error' => 400]);
                
            }
            
        }else 
        {
             return response()->json(['message' => 'User can claim token',
            'success' => 200]);
        }
        
    }
    public function PackageMiningCheck(Request $request)
    {
        $data= UserMining::where('user_id',Auth::id())->orderBy('id','desc')->first();
        $purchase_package= PurchasePackage::where('user_id',Auth::id())->first();
        if($purchase_package == null)
        {
            $user_type= 'New User';
        }else 
        {
            $user_type = 'Existing User';
        }
        if($data != null)
        {
            //dd($data);
            $last_mining = ($data->created_at)->addHours(24);
           // dd($last_mining);
            if(Carbon::now() > $last_mining)
            {
                 return response()->json(['message' => 'User can claim token',
                 'user_type' => $user_type,
            'success' => 200]);
                
            }else 
            {
                return response()->json(['message' => 'User cannot claim token',
                'start_time'=> $data->created_at,
            'end_time'=> $last_mining,
             'user_type' => $user_type,
               
            'error' => 400]);
                
            }
            
        }else 
        {
             return response()->json(['message' => 'User can claim token',
              'user_type' => $user_type,
            'success' => 200]);
        }
        
    }
     public function ConversionCheck()
    {
        $data= BizTWallet::where('method','Biz Conversion')->where('user_id',Auth::id())->orderBy('id','desc')->first();
       // dd($data);
        $convert_setting= ConvertSetting::first();
        if($data != null)
        {
            $last_conversion = ($data->created_at)->addDays($convert_setting->duration);
            if(Carbon::now() > $last_conversion)
            {
                 return response()->json(['message' => 'User can convert token',
            'success' => 200]);
                
            }else 
            {
                return response()->json(['message' => 'User cannot convert token',
                 'start_time'=> $data->created_at,
            'end_time'=> $last_conversion,
            'error' => 400]);
                
            }
            
        }else 
        {
             return response()->json(['message' => 'User can convert token',
            'success' => 200]);
        }
        
    }
    public function convertbiz(Request $request)
    {
        $biz_balance= BizWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
        $convert_setting= ConvertSetting::first();
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $txn_id = substr(str_shuffle($chars), 0, 11);
        if($biz_balance < $request->amount)
        {
             return response()->json(['message' => 'You don\'t have enough balance to convert',
            'error' => 400]);
            
        };
        if($request->amount < $convert_setting->minimum_convert)
        {
            return response()->json(['message' => 'Minimum amount allowed for conversion is '.$convert_setting->minimum_convert,
            'error' => 400]);
            
        };
        if($request->amount > $convert_setting->maximum_convert)
        {
            return response()->json(['message' => 'Maximum amount allowed for conversion is '.$convert_setting->maximum_convert,
            'error' => 400]);
            
        };
         $data= BizTWallet::where('method','Biz Conversion')->where('user_id',Auth::id())->orderBy('id','desc')->first();
       // dd($data);
        //$convert_setting= ConvertSetting::first();
        if($data != null)
        {
            $last_conversion = ($data->created_at)->addDays($convert_setting->duration);
            if(Carbon::now() > $last_conversion)
            {
                $deduct = new BizWallet();
                $deduct->user_id = Auth::id();
                $deduct->type = 'Credit';
                $deduct->method = 'Withdraw';
                $deduct->status = 'approved';
                $deduct->amount = -(($amount) + ($amount*$convert_setting->charge/100));
                $deduct->txn_id = $txn_id;
               // $deposit->wallet_id = $validatedData['wallet_id'];
                $deduct->description= $request->amount . ' Converted to BIZT token';
                $deduct->save();
                $convert = new BizTWallet();
                $convert->user_id = Auth::id();
                $convert->type = 'Debit';
                $convert->method = 'Biz Conversion';
                $convert->status = 'approved';
                $convert->amount = $amount;
                $convert->txn_id = $txn_id;
               // $deposit->wallet_id = $validatedData['wallet_id'];
                $convert->description= $request->amount . ' Converted from BIZ token';
                $convert->save();
                
                
                 return response()->json(['message' => 'Successfully converted',
            'success' => 200]);
                
            }else 
            {
                return response()->json(['message' => 'User cannot convert token',
                 'start_time'=> $data->created_at,
            'end_time'=> $last_conversion,
            'error' => 400]);
                
            }
            
        }else 
        {
                $deduct = new BizWallet();
                $deduct->user_id = Auth::id();
                $deduct->type = 'Credit';
                $deduct->method = 'Withdraw';
                $deduct->status = 'approved';
                $deduct->amount = -(($amount) + ($amount*$convert_setting->charge/100));
                $deduct->txn_id = $txn_id;
               // $deposit->wallet_id = $validatedData['wallet_id'];
                $deduct->description= $request->amount . ' Converted to BIZT token';
                $deduct->save();
                $convert = new BizTWallet();
                $convert->user_id = Auth::id();
                $convert->type = 'Debit';
                $convert->method = 'Biz Conversion';
                $convert->status = 'approved';
                $convert->amount = $amount;
                $convert->txn_id = $txn_id;
               // $deposit->wallet_id = $validatedData['wallet_id'];
                $convert->description= $request->amount . ' Converted from BIZ token';
                $convert->save();
                
                
                 return response()->json(['message' => 'Successfully converted',
            'success' => 200]);
        }
        
    }
    public function ClaimFreeMining()
    {
    $comission= ComissionSetting::first();
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $txn_id = substr(str_shuffle($chars), 0, 11);
    $bonus = new BizWallet();
    $bonus->user_id = Auth::id();
    $bonus->type = 'Debit';
    $bonus->method = 'Daily Mining';
    $bonus->status = 'approved';
    $bonus->amount = $comission->free_mining_rewards;
    $bonus->txn_id = $txn_id;
   // $bonus->wallet_id = $validatedData['wallet_id'];
    $bonus->description= $comission->free_mining_rewards.' daily Biztoken claimed by '.Auth::user()->name .'('.Auth::user()->email.')';
    $bonus->save();
    $end_time= ($bonus->created_at)->addHours(24);
     return response()->json(['message' => 'Successfully claimed token',
       'start_time'=> $bonus->created_at,
            'end_time'=> $end_time,
            'success' => 200]);
        
    }
    public function StartMining()
    {
        
      $purchases= PurchasePackage::where('user_id',Auth::id())->where('status',0)->get();
      foreach($purchases as $purchase)
      {
        $mining= new UserMining();
      $mining->package_id = $purchase->package_id;
      $mining->user_id= Auth::id();
      $mining->save();
      
     
          
      }
       $last_mining = (Carbon::now())->addHours(24);
      
        return response()->json(['message' => 'Successfully started mining',
        'start_time'=> Carbon::now(),
            'end_time'=> $last_mining,
        
          
            'success' => 200]);
     
      
      
    }
 
    
}
