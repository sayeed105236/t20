<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminWallet;

class AdminWalletController extends Controller
{
    public function index()
    {
        $wallets= AdminWallet::where('is_deleted',0)->get();
        return response()->json([$wallets]);
    }
    
    public function store(Request $request)
    
    {
        $rules = [
        'wallet_name' => 'required|string',
        'wallet_no' => 'required',
        'network'=> 'required',
        'min_token'=> 'required|numeric',
        'max_token'=>'required|numeric',

    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $wallet = new AdminWallet();
    $wallet->wallet_name = $validatedData['wallet_name'];
    $wallet->wallet_no = $validatedData['wallet_no'];
    $wallet->network = $validatedData['network'];
    $wallet->min_token = $validatedData['min_token'];
    $wallet->max_token = $validatedData['max_token'];
   
    $wallet->save();
     
    // Return the saved data as JSON response
    return response()->json(['status' => 200]);
        
    }
    public function update(Request $request)
    
    {
        $rules = [
        'wallet_name' => 'required',
        'wallet_no' => 'required',
         'network'=> 'required',
        'min_token'=> 'required|numeric',
        'max_token'=>'required|numeric',
     
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $wallet = AdminWallet::where('id',$request->id)->first();
    $wallet->wallet_name = $validatedData['wallet_name'];
    $wallet->wallet_no = $validatedData['wallet_no'];
    $wallet->status= $request->status;
    $wallet->network = $validatedData['network'];
    $wallet->min_token = $validatedData['min_token'];
    $wallet->max_token = $validatedData['max_token'];
   
    $wallet->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
        
    }
    
    public function delete($id)
    {
        $wallet= AdminWallet::find($id);
        $wallet->is_deleted = 1;
        $wallet->save();
        return response()->json(['success' => 200]);
        
    }
}
