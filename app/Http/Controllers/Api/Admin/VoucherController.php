<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VoucherSetting;
use App\Models\PurhchaseVoucher;
use App\Models\UsdtWallet;


class VoucherController extends Controller
{
     public function index()
    {
        $vouchers= VoucherSetting::where('is_deleted',0)->get();
        return response()->json([$vouchers]);
    }
     public function indexUser()
    {
        $vouchers= VoucherSetting::where('is_deleted',0)->where('status',1)->get();
        return response()->json([$vouchers]);
    }
    
    public function store(Request $request)
    
    {
        $rules = [
        'name' => 'required|string',
        'price' => 'required|numeric',
        'validity' => 'required',
        'charge' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
  
    $voucher = new VoucherSetting();
    $voucher->name = $validatedData['name'];
    $voucher->price = $validatedData['price'];
    $voucher->validity = $validatedData['validity'];
    $voucher->charge = $validatedData['charge'];
    $voucher->status= $request->status;
    $voucher->save();
     
    // Return the saved data as JSON response
    return response()->json(['voucher' => $voucher]);
        
    }
    public function update(Request $request)
    
    {
     
        $rules = [
        'name' => 'required|string',
        'price' => 'required|numeric',
        'validity' => 'required',
        'charge' => 'required',
        
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
  
    $voucher = VoucherSetting::where('id',$request->id)->first();
    $voucher->name = $validatedData['name'];
    $voucher->price = $validatedData['price'];
    $voucher->validity = $validatedData['validity'];
    $voucher->charge = $validatedData['charge'];
    $voucher->status= $request->status;
    $voucher->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
        
    }
    
    public function delete($id)
    {
        $voucher= VoucherSetting::find($id);
        $voucher->is_deleted = 1;
        $voucher->save();
        return response()->json(['success' => 200]);
        
    }
    
}
