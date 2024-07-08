<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserWallet;
use App\Models\BizWallet;
use App\Models\UsdtWallet;
use App\Models\A2IWallet;
use Auth;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\BizTWallet;
use App\Models\PurchasePackage;

class UserWalletController extends Controller
{
     public function index()
    {
        $wallets= UserWallet::where('is_deleted',0)->get();
        return response()->json([$wallets]);
    }
    public function balances()
    {
        
        $usdt_balance= UsdtWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
        $biz_balance = BizWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
        $a2i_balance = A2IWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
        $bizt_balance = BizTWallet::where('user_id',Auth::id())->where('status','approved')->sum('amount');
        $data = [
    [
        'name' => 'USDT',
        'image'=>'https://mining.bizex.io/public/storage/wallets/A2I.jpg',
        'balance' => round($usdt_balance,3)
    ],
    [
        'name' => 'BIZ TOKEN',
          'image'=>'https://mining.bizex.io/public/storage/wallets/BIZ_Token.jpg',
        'balance' => round($biz_balance,3)
    ],
    [
        'name' => 'A2I TOKEN',
          'image'=>'https://mining.bizex.io/public/storage/wallets/A2I.jpg',
        'balance' => round($a2i_balance,3)
    ],
    [
        'name' => 'BIZT COIN',
        'image'=> 'https://mining.bizex.io/public/storage/wallets/BIZT.jpg',
        'balance' => round($bizt_balance,3)
    ]
];

// Return JSON response
return response()->json($data);
    }
    public function UserLevel()
    {
        
        $level_1_users = User::where('sponsor', Auth::user()->referral_code)->where('is_verified',1)->get();
$level_1_count = User::where('sponsor', Auth::user()->referral_code)->where('is_verified',1)->count();
//$all_levels_count = Auth::user()->free_member;

$level_2_count = 0;
$level_3_count = 0;
$level_2_users = collect(); // Using a collection to easily handle appending
$level_3_users = collect();

foreach ($level_1_users as $row1) {
    $level_2_user_set = User::where('sponsor', $row1->referral_code)->where('is_verified',1)->get();
    $level_2_users = $level_2_users->merge($level_2_user_set); // Merge the collections
    $level_2_count += $level_2_user_set->count();
   // $all_levels_count += $level_2_user_set->count();

    foreach ($level_2_user_set as $row2) {
        $level_3_user_set = User::where('sponsor', $row2->referral_code)->where('is_verified',1)->get();
        $level_3_users = $level_3_users->merge($level_3_user_set); // Merge the collections
        $level_3_count += $level_3_user_set->count();
       // $all_levels_count += $level_3_user_set->count();
    }
}
$initial_users = User::where('sponsor', Auth::user()->referral_code)->where('is_verified', 1)->get();
    $level_users = collect([$initial_users]); // Initialize with the first level users
    $level_counts = collect([$initial_users->count()]); // Initialize with the first level count

    $all_levels_count = $initial_users->count();

    $current_level_users = $initial_users;
    $current_level_index = 1;

    // Process levels dynamically
    while ($current_level_users->isNotEmpty()) {
        $next_level_users = collect();
        foreach ($current_level_users as $user) {
            $user_sponsors = User::where('sponsor', $user->referral_code)->where('is_verified', 1)->get();
            $next_level_users = $next_level_users->merge($user_sponsors);
        }
        if ($next_level_users->isNotEmpty()) {
            $level_users->push($next_level_users);
            $level_counts->push($next_level_users->count());
            $all_levels_count += $next_level_users->count();
        }
        $current_level_users = $next_level_users;
        $current_level_index++;
    }

$data = [
        [
        'level' => 'All Levels',
        'users' => $all_levels_count
    ],
    [
        'level' => 'Level 1',
        'users' => $level_1_count,
        'level_1_users' => $level_1_users
    ],
    [
        'level' => 'Level 2',
        'users' => $level_2_count,
        'level_2_users' => $level_2_users
    ],
    [
        'level' => 'Level 3',
        'users' => $level_3_count,
        'level_3_users' => $level_3_users
    ]
];



    // Prepare data for response


   
// Return JSON response
return response()->json($data);
    }
    
    public function store(Request $request)
    
    {
        $rules = [
        'main_wallet_id' => 'required|numeric',
        'wallet_address' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $wallet = new UserWallet();
    $wallet->user_id = Auth::id();
    $wallet->main_wallet_id = $validatedData['main_wallet_id'];
    $wallet->wallet_address = $validatedData['wallet_address'];
   
    $wallet->save();
     
    // Return the saved data as JSON response
    return response()->json(['status' => 200]);
        
    }
    public function update(Request $request)
    
    {
        $rules = [
        'main_wallet_id' => 'required|numeric',
        'wallet_address' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $wallet = UserWallet::where('id',$request->id)->first();
    $wallet->status= $request->status;
    $wallet->user_id = Auth::id();
    $wallet->main_wallet_id = $validatedData['main_wallet_id'];
    $wallet->wallet_address = $validatedData['wallet_address'];
   
    $wallet->save();
   
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
        
    }
    
    public function delete($id)
    {
        $wallet= UserWallet::find($id);
        $wallet->is_deleted = 1;
        $wallet->save();
        return response()->json(['success' => 200]);
        
    }
    public function UsdtHistory()
    {
        $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone')
        ->join('users','users.id','usdt_wallets.user_id')
        ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.user_id',Auth::id())->get();
       // dd($history);
         return response()->json([
            'usdt_wallet_history'=>$history,
         'success' => 200]);
    }
    public function BizHistory()
    {
         $history = BizWallet::select('biz_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','biz_wallets.user_id')
        ->where('biz_wallets.status','approved')
        ->where('biz_wallets.user_id',Auth::id())->get();
         return response()->json([
            'biz_wallet_history'=>$history,
         'success' => 200]);
    }
    public function A2IHistory()
    {
         $history = A2IWallet::select('a2_i_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','a2_i_wallets.user_id')
        ->where('a2_i_wallets.status','approved')
        ->where('a2_i_wallets.user_id',Auth::id())->get();
         return response()->json([
            'a2i_wallet_history'=>$history,
         'success' => 200]);
    }
     public function BizTHistory()
    {
         $history = BizTWallet::select('biz_t_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','biz_t_wallets.user_id')
        ->where('biz_t_wallets.status','approved')
        ->where('biz_t_wallets.user_id',Auth::id())->get();
         return response()->json([
            'bizt_wallet_history'=>$history,
         'success' => 200]);
    }
     public function DepositHistory()
    {
         $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone','usdt_wallets.created_at as date')
         ->join('users','users.id','usdt_wallets.user_id')
      //  ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.method','Deposit')
        ->where('usdt_wallets.user_id',Auth::id())->get();
         return response()->json([
            'deposit_history'=>$history,
         'success' => 200]);
    }
       public function WithdrawHistory()
    {
         $history = Withdraw::select('withdraws.*','users.name','users.email','users.phone')
         ->join('users','users.id','withdraws.user_id')
       // ->where('withdraws.status','approved')
        //->where('withdraws.method','Deposit')
        ->where('withdraws.user_id',Auth::id())->get();
         return response()->json([
            'withdraw_history'=>$history,
         'success' => 200]);
    }
    public function TransferHistory()
    {
         $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','usdt_wallets.user_id')
        ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.method','Transfer')
        ->where('usdt_wallets.user_id',Auth::id())->get();
         return response()->json([
            'transfer_history'=>$history,
         'success' => 200]);
    }
        public function ReferHistory()
    {
         $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','usdt_wallets.user_id')
        ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.method','Sponsor Bonus')
        ->where('usdt_wallets.user_id',Auth::id())->get();
         return response()->json([
            'refer_bonus_history'=>$history,
         'success' => 200]);
    }
           public function LevelHistory()
    {
         $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','usdt_wallets.user_id')
        ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.method','Level Bonus')
        ->where('usdt_wallets.user_id',Auth::id())->get();
         return response()->json([
            'level_bonus_history'=>$history,
         'success' => 200]);
    }
     public function LeadershipHistory()
    {
         $history = UsdtWallet::select('usdt_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','usdt_wallets.user_id')
        ->where('usdt_wallets.status','approved')
        ->where('usdt_wallets.method','Leadership Bonus')
        ->where('usdt_wallets.user_id',Auth::id())->get();
         return response()->json([
            'level_bonus_history'=>$history,
         'success' => 200]);
    }
           public function FreeMiningHistory()
    {
         $history = BizWallet::select('biz_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','biz_wallets.user_id')
        ->where('biz_wallets.status','approved')
        ->where('biz_wallets.method','Daily Mining')
        ->where('biz_wallets.user_id',Auth::id())->get();
         return response()->json([
            'free_bonus_history'=>$history,
         'success' => 200]);
    }
     public function PackageMiningHistory()
    {
         $history = BizWallet::select('biz_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','biz_wallets.user_id')
        ->where('biz_wallets.status','approved')
        ->where('biz_wallets.method','Daily Package Bonus')
        ->where('biz_wallets.user_id',Auth::id())->get();
         return response()->json([
            'daily_package_bonus_history'=>$history,
         'success' => 200]);
    }
        public function A2ITokenHistory()
    {
         $history = A2IWallet::select('a2_i_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','a2_i_wallets.user_id')
        ->where('a2_i_wallets.status','approved')
        ->where('a2_i_wallets.method','A2I Bonus')
        ->where('a2_i_wallets.user_id',Auth::id())->get();
         return response()->json([
            'daily_a2i_bonus_history'=>$history,
         'success' => 200]);
    }
     public function FreelevelHistory()
    {
         $history = BizWallet::select('biz_wallets.*','users.name','users.email','users.phone')
         ->join('users','users.id','biz_wallets.user_id')
        ->where('biz_wallets.status','approved')
        ->where('biz_wallets.method','Level Bonus')
        ->where('biz_wallets.user_id',Auth::id())->get();
         return response()->json([
            'free_level_bonus_history'=>$history,
         'success' => 200]);
    }
    public function DepositCheck()
    {
        $data= UsdtWallet::where('user_id',Auth::id())->where('method','Deposit')->where('status','approved')->count();
        $purchase_status = PurchasePackage::where('user_id',Auth::id())->count();
        if($data > 0 || $purchase_status > 0 )
        {
            return response()->json([
            'message'=>'User can use USDT wallet',
         'success' => 200]);
            
        }else 
        {
              return response()->json([
            'message'=>'User can not use USDT wallet',
         'error' => 400]);
            
        }
         
        
    }
    
    
    
}
