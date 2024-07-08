<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawSetting;

class WithdrawSettingController extends Controller
{
    public function index()
    {
        $settings= WithdrawSetting::get();
        return response()->json([$settings]);
    }
    
    public function update(Request $request)
    {
    
    $setting = WithdrawSetting::where('id',$request->id)->first();
    $setting->withdrwal_charge = $request->withdrwal_charge;
    $setting->min_withdraw = $request->min_withdraw;
    $setting->max_withdraw = $request->max_withdraw;
    $setting->status = $request->status;
    $setting->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
    }
}
