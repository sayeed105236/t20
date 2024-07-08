<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsdtWallet;
use App\Models\AdminWallet;
use App\Models\User;
use App\Models\BizWallet;
use Auth;
use App\Models\PackageSetting;
use App\Models\PurchasePackage;
use Carbon\Carbon;
use App\Models\ComissionSetting;
use DB;
use App\Models\A2IWallet;
use App\Models\TransferSetting;
use App\Models\PurchaseVoucher;
use App\Models\VoucherSetting;
Use App\Models\UserMining;
class UsdtWalletController extends Controller
{
    public function index()
    {
        $wallet= UsdtWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
         return response()->json([
             'usdt_balance'=> $wallet]);
    }
    public function AdminWallet()
    {
         $wallet= AdminWallet::where('is_deleted',0)->where('status',1)->get();
         return response()->json(['wallet'=> $wallet]);
        
    }
    public function packages()
    {
        
        $packages = PackageSetting::select('package_settings.id','package_settings.package_name as spinner_name','package_settings.package_price as spinner_price',
    'package_settings.duration as duration','package_settings.created_at as created_at'
    )->where('package_settings.is_deleted',0)->get();
        return response()->json(['spinners'=>$packages]);
    }
    public function PurchasePackage(Request $request)
    {
        $rules = [
        'spinner_id' => 'required|numeric',
      
        
    ];
        $validatedData = $request->validate($rules);
       // $balance= Usdt::where('id',Auth::id())->where('status','approved')->sum('amount');
   
     
        
       
       // dd($balance,Auth::id());
        $package= PackageSetting::where('id',$request->spinner_id)->first();
       // dd(Auth::user()->balance,Auth::user()->id);
       $user= User::where('id',Auth::id())->first();
        if((Auth::user()->balance) < $package->package_price)
        {
             return response()->json(['balance' => $user->balance,
            'failed' => 400,
            'message'=> 'Insufficient Balance']);
            
        }else 
        {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $code = substr(str_shuffle($chars), 0, 6);
      //  DB::Begintransaction();
        $purchase= new PurchasePackage();
        $purchase->user_id = Auth::id();
        $purchase->package_id =  $validatedData['spinner_id'];
        $purchase->code = $code;
        $purchase->date= Carbon::now();
        $purchase->save();
        
        $user->balance = ($user->balance)- ($package->package_price);
        $user->save();
        $start_game= new UserMining();
        $start_game->user_id = Auth::id();
        $start_game->package_id= $request->spinner_id;
        $start_game->save();

        return response()->json(['message'=> 'Spinner purchased successfully',
        'success' => 200]);
           
             
            
        }
       
        
            
        }
        
     
  
    public function PurchaseHistory()
    {
         $purchases= PurchasePackage::select('purchase_packages.date as date','users.name','users.email','package_settings.*','purchase_packages.status as availibility')
         ->join('users','users.id','purchase_packages.user_id')
         ->join('package_settings','package_settings.id','purchase_packages.package_id')
         ->where('purchase_packages.user_id',Auth::id())
         ->get();
         return response()->json([
             'purchase_history'=> $purchases]);
        
    }
    public function AddMoney(Request $request)
    {
         $rules = [
        'amount' => 'required',
        'wallet_id' => 'required',
        'txn_id' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $deposit = new UsdtWallet();
    $deposit->user_id = Auth::id();
    $deposit->type = 'Debit';
    $deposit->method = 'Deposit';
    $deposit->status = 'pending';
    $deposit->amount = $validatedData['amount'];
    $deposit->txn_id = $validatedData['txn_id'];
    $deposit->wallet_id = $validatedData['wallet_id'];
    $deposit->description= $request->amount . ' Deposit requested from '.Auth::user()->name .'('.Auth::user()->email.')';
    $deposit->save();
     
    // Return the saved data as JSON response
    return response()->json(['deposit' => $deposit,
    'success' => 200]);
    }
    
    public function TransferMoney(Request $request)
    {
         $rules = [
        'amount' => 'required',
        'code' => 'required',
       // 'main_wallet_id' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);
     $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $txn_id = substr(str_shuffle($chars), 0, 11);
     $txn_id_2 = substr(str_shuffle($chars), 0, 11);
    $transfer_setting= TransferSetting::first();
    $balance= UsdtWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
    if($transfer_setting->status == 0)
    {
        $charge= 0;
    }else 
    {
        $charge= $transfer_setting->transfer_charge;
    }
    $amount= $request->amount+ $request->amount*($charge/100);
    
    if($balance < $amount)
    {
         return response()->json(['balance' => $balance,
            'failed' => 400,
            'message'=> 'Insufficient Balance']);
    }
    if($balance < $transfer_setting->min_transfer)
    {
         return response()->json(['balance' => $balance,'transfer_setting'=>$transfer_setting,
            'failed' => 400,
            'message'=> 'Amount should be higher or equal to '.$transfer_setting->min_transfer]);
    }
     if($balance > $transfer_setting->max_transfer)
    {
         return response()->json(['balance' => $balance,'transfer_setting'=>$transfer_setting,
            'failed' => 400,
            'message'=> 'Amount should be less or equal to '.$transfer_setting->max_transfer]);
    }
    // Create a new PakageSetting instance and save it
    $user= User::where('refferal_code',$request->code)->first();
    $deduct = new UsdtWallet();
    $deduct->user_id = Auth::id();
    $deduct->type = 'Credit';
    $deduct->method = 'Transfer';
    $deduct->received_from = Auth::id();
    $deduct->received_by = $user->id;
    $deduct->status = 'approved';
    $deduct->amount = -($amount);
    $deduct->txn_id = $txn_id;
   // $deposit->wallet_id = $validatedData['wallet_id'];
    $deduct->description= $request->amount . ' transfer from '.Auth::user()->name .'('.Auth::user()->email.') to '.$user->name .'('.$user->email. ')';
    $deduct->save();
    $transfer = new UsdtWallet();
    $transfer->user_id = Auth::id();
    $transfer->type = 'Debit';
    $transfer->method = 'Transfer';
    $transfer->received_from = Auth::id();
    $transfer->received_by = $user->id;
    $transfer->status = 'approved';
    $transfer->amount = -($amount);
    $transfer->txn_id = $txn_id_2;
   // $deposit->wallet_id = $validatedData['wallet_id'];
    $transfer->description= $request->amount . ' received from '.Auth::user()->name .'('.Auth::user()->email.') to '.$user->name .'('.$user->email. ')';
    $transfer->save();
     
    // Return the saved data as JSON response
    return response()->json(['deduct' => $deduct,'transfer'=>$transfer,
    'success' => 200]);
    }
    public function PurchaseVoucher(Request $request)
    {
         $rules = [
        'voucher_id' => 'required',
      
        
    ];
        $validatedData = $request->validate($rules);
        $balance= UsdtWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
       // dd($balance,Auth::id());
        $voucher= VoucherSetting::where('id',$request->voucher_id)->first();
        $deposit_status = UsdtWallet::where('user_id',Auth::id())->where('method','Deposit')->where('status','approved')->count();
        $purchase_status = PurchasePackage::where('user_id',Auth::id())->count();
        if($balance < ($voucher->price+ ($voucher->price*$voucher->charge /100)))
        {
             return response()->json(['balance' => $balance,
            'failed' => 400,
            'message'=> 'Insufficient Balance']);
            
        }elseif($deposit_status == 0 && $purchase_status == 0)
        {
            return response()->json(['balance' => $balance,
            'failed' => 400,
            'message'=> 'You have not made any deposit yet']);
            
        }
        
        else 
        {
        DB::Begintransaction();
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $txn_id = substr(str_shuffle($chars), 0, 11);
        $code= substr(str_shuffle($chars), 0, 6);
        $voucher= VoucherSetting::where('id',$request->voucher_id)->first();
        $purchase= new PurchaseVoucher();
        $purchase->user_id = Auth::id();
        $purchase->voucher_id =  $validatedData['voucher_id'];
        $purchase->date= Carbon::now();
        $purchase->validity= Carbon::now()->addDay($voucher->validity);
        $purchase->code= $code;
        $purchase->save();
        $deduct = new UsdtWallet();
        $deduct->user_id = Auth::id();
        $deduct->type = 'Credit';
        $deduct->method = 'Withdraw';
       // $deduct->received_from = Auth::id();
       // $deduct->received_by = $user->id;
        $deduct->status = 'approved';
        $deduct->amount = -($voucher->price +($voucher->price*$voucher->charge/100));
        $deduct->txn_id = $txn_id;
       // $deposit->wallet_id = $validatedData['wallet_id'];
        $deduct->description= 'Purchased voucher code '.$code;
        $deduct->save();
        DB::commit();
        return response()->json(['deduct' => $deduct,
    'success' => 200]);
         }
        
    }
    public function VoucherPurchaseHistory()
    {
         $purchases= PurchaseVoucher::select('purchase_vouchers.date as date','users.name','users.email','voucher_settings.*','purchase_vouchers.status as validity',
         'purchase_vouchers.code')
         ->join('users','users.id','purchase_vouchers.user_id')
         ->join('voucher_settings','voucher_settings.id','purchase_vouchers.voucher_id')
         ->where('purchase_vouchers.user_id',Auth::id())
         ->get();
         return response()->json([
             'purchase_history'=> $purchases]);
        
    }
    public function autostore(Request $request)
    {
         $rules = [
        'amount' => 'required',
       // 'pay_currency'=> 'required',
        
    ];
    $validatedData = $request->validate($rules);
        $client = new \GuzzleHttp\Client();
      $token= '9S3WW3P-JRB43DN-PBCJ23E-P9W96H3';
      //$token= 'F2QJSJ9-B5YME5J-MW4WBJJ-2M4NSET';

      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $description = substr(str_shuffle($chars), 0, 11);
      $user= Auth::user()->email;
      $headers = [
          // 'Authorization' => 'Bearer ' . $api_key,
          //'x-api-key'        => 'F2QJSJ9-B5YME5J-MW4WBJJ-2M4NSET',
          'x-api-key'        => '9S3WW3P-JRB43DN-PBCJ23E-P9W96H3',
          'Content-Type' => 'application/json',



      ];
       
$url = 'https://api.nowpayments.io/v1/min-amount';
$queryParams = [
    'currency_from' => 'usdttrc20',
    'currency_to' => 'trx',
    'fiat_equivalent' => 'usd',
    'is_fixed_rate' => 'false',
    'is_fee_paid_by_user' => 'false',
];

$response = $client->request('GET', $url, [
    'headers' => $headers,
    'query' => $queryParams,
]);

$check_amount = $response->getBody()->getContents();
$data2 = json_decode($check_amount, true);
if($request->amount < $data2['min_amount'])
{
    return response()->json([
    'message'=> 'Minimum amount allow for deposit is '.$data2['min_amount'],
    'error' => 400]);
    
}


//       curl --location 'https://api.nowpayments.io/v1/min-amount?currency_from=eth&currency_to=trx&fiat_equivalent=usd&is_fixed_rate%20=False&is_fee_paid_by_user=False' \
// --header 'x-api-key: <your_api_key>'



      //Duplicate these three lines for calling other api

      $payment = $client->request('POST','https://api.nowpayments.io/v1/invoice', [
              'headers' => $headers,
              'json' => [

                "price_amount"=> $validatedData['amount'],
                "price_currency"=> "usd",
                "pay_currency"=> "usdtbsc",
                //"pay_currency"=> $validatedData['pay_currency'],
                "ipn_callback_url"=> "https://biztoken.fecotrade.com/",
                "success_url"=> "https://biztoken.fecotrade.com/api/user/approve_fund/".$request['amount'].'/'. $description. '/'. $user,
                "cancel_url"=> "https://biztoken.fecotrade.com/api/user/add-fund/cancel",
                "order_id"=> $description,
              //  "payout_currency" => 'bsc',
                "order_description"=> $user,

            ]

         ]);
         


    $payment=$payment->getBody()->getContents();
   // dd($payment);




    $data = json_decode($payment, true);
   // dd($data);
       return response()->json(['url'=>$data['invoice_url'],
       'data'=> $data,
    'success' => 200]);


    //return redirect($data['invoice_url']);
        
    }
     public function approveFund($amount,$description,$user)
  {
      $user_id = User::where('email',$user)->first();

      $deposit = new UsdtWallet();
    $deposit->user_id = $user_id->id;
    $deposit->type = 'Debit';
    $deposit->method = 'Deposit';
    $deposit->status = 'approved';
    $deposit->amount = $amount;
    $deposit->txn_id = $description;
    //$deposit->wallet_id = $validatedData['wallet_id'];
    $deposit->description= $amount . ' Deposited successfully by payment gateway';;
    $deposit->save();
     
    // Return the saved data as JSON response
    return response()->json(['deposit' => $deposit,
    'success' => 200]);
  }
  public function cancel()
  {
      return response()->json([
    'message'=> 'Deposit request failed',
    'error' => 400]);
      
  }
  public function initiateBinancePay(){
    // Generate nonce string
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nonce = '';
    for($i=1; $i <= 32; $i++)
    {
        $pos = mt_rand(0, strlen($chars) - 1);
        $char = $chars[$pos];
        $nonce .= $char;
    }
    $ch = curl_init();
    $timestamp = round(microtime(true) * 1000);
    // Request body
     $request = array(
       "env" => array(
             "terminalType" => "APP" 
          ), 
       "merchantTradeNo" => mt_rand(982538,9825382937292), 
       "orderAmount" => 25.17, 
       "currency" => "BUSD", 
       "goods" => array(
                "goodsType" => "01", 
                "goodsCategory" => "D000", 
                "referenceGoodsId" => "7876763A3B", 
                "goodsName" => "Ice Cream", 
                "goodsDetail" => "Greentea ice cream cone" 
             ) 
    ); 
 
    $json_request = json_encode($request);
   // dd($json_request);
    $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
    $binance_pay_key = "uxd1igyl9g4qmvp5kassihkk5vclhfs5hz9clf882dbpgv5mcyxhi31wirsdylwk";
    $binance_pay_secret = "r7wdyk5pmsad1ncgzmuasxrww6vvrjw7lvohipmog4ua2xukijspjavir5mwoyns";
    $signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "BinancePay-Timestamp: $timestamp";
    $headers[] = "BinancePay-Nonce: $nonce";
    $headers[] = "BinancePay-Certificate-SN: $binance_pay_key";
    $headers[] = "BinancePay-Signature: $signature";

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, "https://bpay.binanceapi.com/binancepay/openapi/v2/order");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

    $result = curl_exec($ch);
    if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
    curl_close ($ch);

    dd($result);
    //app_key= "H6h4mWf8qKwLVE3yxlO4wdf3UNFCE8zTrtekYj8gn55oF5seYeDwX9PiTOpOmzfM";
    //secret_key= "VV8YGoPy6H1izXzoBt6tLfYCiuJ27bcNOmFwgbIzj4jxWFcr25hekn2nzwSZDmxC";

    //Redirect user to the payment page

}
    
}
