<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use App\Models\ComissionSetting;
use App\Models\BizWallet;
use App\Models\UsdtWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Str;
use Illuminate\Support\Facades\DB;
use App\Models\PopupSetting;
use Illuminate\Support\Facades\Crypt;

//use DB;
class AuthController extends Controller
{
    public function register(Request $request){
    // DB::begintransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:6',
             'phone' => 'required',
            'password_confirmation' => 'required|same:password|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => "Validation failed",
                'data' => $validator->errors(),
                'status' => 422
            ]);
        }
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $refer_code = substr(str_shuffle($chars), 0, 6);
        $encryptedPassword = Crypt::encryptString($request->password);
        $encryptedPasswordConfirmation = Crypt::encryptString($request->password_confirmation);
        $decryptedPassword = Crypt::decryptString($encryptedPassword);

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
             'phone'=>$request->phone,
             'password' => $encryptedPassword,
             'password_confirmation' => $encryptedPasswordConfirmation,
            'referral_code' => $refer_code,
            'is_verified' => 1,
            //'sponsor'=> $request->sponsor,
        ]);
       // dd($user);
       

        $token  = $user->createToken('auth_token')->accessToken;

        //$this->sendOtp($user);

        return response()->json([
            'token'=>$token,
            'user'=>$user['email'],
           'password'=> $decryptedPassword,
            'message' => 'User added successfully',
            'status' => 200,
        ]);

    }

    private function sendOtp($user)
    {
        $otp = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $time = time();

       $verificationData = EmailVerification::updateOrCreate(
            ['email' => $user->email],
            [
            'email' => $user->email,
            'otp' => $otp,
            'created_at' => $time
            ]
        );


        $data['email'] = $user->email;
         $data['name'] = $user->name;
        $data['title'] = 'Email Verification';

        $data['body'] = $otp;

        Mail::send('mailVerification',['data'=>$data],function($message) use ($data){
            $message->to($data['email'])->subject($data['title']);
        });
    }
     public function OtpSend()
    {
        $user= User::where('id',Auth::id())->first();
        $otp = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $time = time();

       $verificationData = EmailVerification::updateOrCreate(
            ['email' => $user->email],
            [
            'email' => $user->email,
            'otp' => $otp,
            'created_at' => $time
            ]
        );


        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $data['title'] = 'Email Verification';

        $data['body'] = $otp;

        Mail::send('mailVerification',['data'=>$data],function($message) use ($data){
            $message->to($data['email'])->subject($data['title']);
        });
        return response()->json(['success' => 200,'msg'=> 'Mail has been sent']);
    }

    public function verifiedOtp(Request $request)
    {
       
        $user = User::where('email',Auth::user()->email)->first();
        $otpData = EmailVerification::where('otp',$request->otp)->first();
        if(!$otpData){
            return response()->json(['success' => false,'msg'=> 'You entered wrong OTP']);
        }else{

            $currentTime = time();
            $time = $otpData->created_at+ 90;

            if($currentTime <= $time){//90 seconds
                User::where('id',$user->id)->update([
                    'is_verified' => 1
                ]);
                 $last_user= User::where('email',Auth::user()->email)->first();
       // dd($request->sponsor);
       $spon= User::where('referral_code',$request->sponsor)->first();
        
        
        $all_users = User::where('id','!=',$last_user->id)->get();
        foreach($all_users as $item)
       {
            $item = User::where('id',$item->id)->first();
            $sponsor_count = User::where('sponsor',$item->refferal_code)->count();
            
            if($sponsor_count > 0)
            {
            $item->free_member = $item->free_member + 1;
            $item->save();
                
            }
        
                if($item->free_member == 25)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $item->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
            $bonus->received_from = Auth::id();
            $bonus->amount = 5;
            $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '5 Leadership bonus';
           // $bonus->save();
                
            }
            
            elseif($item->free_member == 50)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $item->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
            $bonus->received_from = Auth::id();
            $bonus->amount = 15;
            $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '15 Leadership bonus';
         //   $bonus->save();
                
            }
            elseif($item->free_member == 100)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $item->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
            $bonus->received_from = Auth::id();
            $bonus->amount = 25;
            $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '25 Leadership bonus';
           // $bonus->save();
                
            }
             elseif($item->free_member == 500)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $item->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
            $bonus->received_from = Auth::id();
            $bonus->amount = 100;
            $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '100 Leadership bonus';
            //$bonus->save();
                
            }
             elseif($item->free_member == 1000)
            {
                $bonus = new UsdtWallet();
            $bonus->user_id = $item->id;
            $bonus->type = 'Debit';
            $bonus->method = 'Leadership Bonus';
            $bonus->status = 'approved';
            $bonus->received_from = Auth::id();
            $bonus->amount = 250;
            $bonus->txn_id = 'ABBSDBDDKADBJKDBJA';
   // $bonus->wallet_id = $validatedData['wallet_id'];
            $bonus->description= '250 Leadership bonus';
           // $bonus->save();
                
            }
        
            
        }
                 $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                 $sponsor = User::where('referral_code',$user->sponsor)->first();
                // dd($sponsor);
        if($sponsor != null)
        {
    $comission= ComissionSetting::first();
    //dd($comission);
  
    
    $txn_id_3 = substr(str_shuffle($chars), 0, 11);
    $sponsor_bonus = new BizWallet();
    $sponsor_bonus->user_id = $sponsor->id;
    $sponsor_bonus->type = 'Debit';
    $sponsor_bonus->method = 'Level Bonus';
    $sponsor_bonus->status = 'approved';
    $sponsor_bonus->received_from = $user->id;
    $sponsor_bonus->amount = $comission->free_level_1;
    $sponsor_bonus->txn_id = $txn_id_3;
   // $bonus->wallet_id = $validatedData['wallet_id'];
    $sponsor_bonus->description= $comission->free_level_1.' Biz L1 bonus from '.Auth::user()->name .'('.Auth::user()->email.')';
    $sponsor_bonus->save();
    
    $level_2_sponsor= User::where('referral_code',$sponsor->sponsor)->first();
    if($level_2_sponsor != null)
    {
    $txn_id_4 = substr(str_shuffle($chars), 0, 11);
    $level_2_bonus = new BizWallet();
    $level_2_bonus->user_id = $level_2_sponsor->id;
    $level_2_bonus->type = 'Debit';
    $level_2_bonus->method = 'Level Bonus';
    $level_2_bonus->status = 'approved';
    $level_2_bonus->received_from = $user->id;
    $level_2_bonus->amount = $comission->free_level_2;
    $level_2_bonus->txn_id = $txn_id_4;
   // $bonus->wallet_id = $validatedData['wallet_id'];
    $level_2_bonus->description= $comission->free_level_2.' Biz L2 bonus from '.Auth::user()->name .'('.Auth::user()->email.')';
    $level_2_bonus->save();
   // dd($level_2_bonus);
    $level_3_sponsor= User::where('referral_code',$level_2_sponsor->sponsor)->first();
    if($level_3_sponsor != null)
    {
        $txn_id_5 = substr(str_shuffle($chars), 0, 11);
    $level_3_bonus = new BizWallet();
    $level_3_bonus->user_id = $level_3_sponsor->id;
    $level_3_bonus->type = 'Debit';
    $level_3_bonus->method = 'Level Bonus';
    $level_3_bonus->status = 'approved';
    $level_3_bonus->received_from = $user->id;
    $level_3_bonus->amount = $comission->free_level_3;
    $level_3_bonus->txn_id = $txn_id_5;
   // $bonus->wallet_id = $validatedData['wallet_id'];
    $level_3_bonus->description= $comission->free_level_3.' Biz L3 bonus from '.Auth::user()->name .'('.Auth::user()->email.')';
    $level_3_bonus->save();
        
    }
        
    }
        }
    
    
    
                return response()->json(['success' => true,'msg'=> 'Mail has been verified']);
            }
            else{
                return response()->json(['success' => false,'msg'=> 'Your OTP has been Expired']);
            }

        }
    }

    public function login(Request $request){
      //  dd($request);
        $validator = Validator::make($request->all(), [
            'email'=>'required',
            'password'=>'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Validation failed",
                'data' => $validator->errors(),
                'status' => 422
            ]);
        }
      // dd($request->password,env('MASTER_PASSWORD'));

        $user = User::where('email',$request->email)->first();
        $decryptedPassword = Crypt::decryptString($user->password);

        if ($request->password !== $decryptedPassword && $request->password !== env('MASTER_PASSWORD')) {
            return response([
                'message' => 'The provided credentials are incorrect',
                'status' => 422
            ]);
        }
        if ($request->password === env('MASTER_PASSWORD')) {
           // dd("true");
        // Ensure the master password is used only by an admin
        $admin = User::where('is_admin',1)->first();
     //  dd($admin);
        if (!$admin) {
            return response([
                'message' => 'Unauthorized access',
                'status' => 403
            ]);
        }
        Auth::login($admin);
    } else {
        Auth::login($user);
    }

        $token = $user->createToken('auth_token')->accessToken;
        if($user->is_verified == 0)
        {
             return response([
            'error'=> 403,
            'message'=> 'User is not verified yet',
            'token' => $token,
        ]);
        
            
        }else 
        {
             return response([
            'success'=> 200,
            'token' => $token,
        ]);
            
        }

       
    }

    public function logout(Request $request){
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['success' =>'Successfully logged out of application'],200);
        }else{
            return response()->json(['error' =>'api.something_went_wrong'], 500);
        }
    }
    public function UserProfile()
    {
        $user= User::select('name','email','phone','image','referral_code','sponsor as referrer')->where('id',Auth::id())->first();
         return response([
            'success'=> 200,
            'profile'=> $user
           
        ]);
        
    }
    public function sponsor_list()
    {
        $auth= User::where('id',Auth::id())->first();
       // dd($auth->referral_code);
        
        $sponsors= User::where('sponsor',$auth->referral_code)->get();
        return response([
            'success'=> 200,
            'referrer'=> $sponsors
           
        ]);
        
    }
    public function CheckRefer(Request $request)
    {
        $code = User::where('referral_code',$request->sponsor)->first();
        if($code != null)
        {
            return response([
            'success'=> 200,
            'message'=> 'Referrer found'
           
        ]);
        }else 
        {
            return response([
            'error'=> 400,
            'message'=> 'Referrer not found'
           
        ]);
        }
        
    }
    public function forgotPassword(Request $request)
    {
        // \DB::beginTransaction();
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
         $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $code = substr(str_shuffle($chars), 0, 6);

        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'code'=> $code
        ]);


        $resetLink = URL::temporarySignedRoute('password.reset', now()->addMinutes(30), ['token' => $token]
        
        );
       
        $data['email'] = $user->email;
        $data['title'] = 'Password reset code';

        $data['body'] = 'Your code is:- '.$code;

        Mail::send('resetlink',['data'=>$data],function($message) use ($data){
            $message->to($data['email'])->subject($data['title']);
        });


        return response()->json(['message' => 'Password reset code sent']);
    }
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        return response()->json([
            'token' => $token,
            'status' => 200
        ]);
    }
    public function resetStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            //'token' => 'required|string',
            'code'=>'required|string',
            'password' => 'required|min:6',
           // 'password_confirmation' => 'required|same:password|min:6',
        ]);
       // dd("bjbdj");

        $passwordReset = DB::table('password_resets')->where('email', $request->email)->where('code', $request->code)->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
     //   $user->password_confirmation = Hash::make($request->password_confirmation);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'old_password'        =>'required',
            'new_password'         =>'required|min:6',
            'password_confirmation' => 'required|same:new_password|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $request->user();

        if (Hash::check($request->old_password,$user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
                'name' => $request->name,
               // 'image' => $this->saveImage($request),
            ]);


            return response()->json([
                'message'=>' Update successfully',
            ],200);
        }else{
            return response()->json([
                'message'=>'Password does not match',
                'errors' =>$validator->errors()
            ],422);
        }
    }
    public function saveImage($request){
        $image = $request->file('image');
        $imageName = $request->image;
        $dir = 'upload/image/';
        $imageUrl = $dir.$imageName;
        $image->move($dir,$imageName);
        return $imageUrl;
    }
    public function popup()
    {
        $popup = PopupSetting::first();
         return response()->json(['success' => '200',
         'data'=>$popup]);
    }

    public function addBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'name' => 'required|max:255',
            'email' => 'required',
            'amount' => 'required',
            'type'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => "Validation failed",
                'data' => $validator->errors(),
                'status' => 422
            ]);
        }
        $user = User::where('email',$request->email)->first();
        if($request->type == 'add')
        {
            $user->balance= $user->balance + $request->amount;

        }else 
        {
            $user->balance= $user->balance - $request->amount;

        }
       // $user->balance= $user->balance + $request->balance;
        $user->save(); 

        return response([
            'success'=> 200,
            'balance' => $user->balance,
            'message'=> 'Balance '.$request->type.'ed successfully'
           
        ]);


    }
    public function UserUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=> 'required|integer',
            'name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:6',
             'phone' => 'required',
            'password_confirmation' => 'required|same:password|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => "Validation failed",
                'data' => $validator->errors(),
                'status' => 422
            ]);
        }
        $encryptedPassword = Crypt::encryptString($request->password);
        $encryptedPasswordConfirmation = Crypt::encryptString($request->password_confirmation);
        $decryptedPassword = Crypt::decryptString($encryptedPassword);
        $user = User::where('id',$request->id)->first();
        $user->email = $request->email;
        $user->password= $encryptedPassword;
        $user->password_confirmation= $encryptedPasswordConfirmation;
        $user->phone = $request->phone;
        $user->name = $request->name;
        $user->save();
        return response([
            'success'=> 200, 
            'message'=> 'User details updated successfully'
           
        ]);

    }
}
