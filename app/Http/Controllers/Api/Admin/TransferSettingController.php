<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransferSetting;

class TransferSettingController extends Controller
{
    public function index()
    {
        $settings= TransferSetting::get();
        return response()->json([$settings]);
    }
    
    public function update(Request $request)
    {
    
    $setting = TransferSetting::where('id',$request->id)->first();
    $setting->transfer_charge = $request->transfer_charge;
    $setting->min_transfer = $request->min_transfer;
    $setting->max_transfer = $request->max_transfer;
    $setting->status = $request->status;
    $setting->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
    }
}
