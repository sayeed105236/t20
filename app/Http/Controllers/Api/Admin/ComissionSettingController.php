<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComissionSetting;
use App\Models\LeadershipSetting;
use App\Models\ConvertSetting;
use App\Models\PopupSetting;
class ComissionSettingController extends Controller
{
    public function index()
    {
        $settings= ComissionSetting::get();
        return response()->json([$settings]);
    }
     public function indexUser()
    {
        $settings= ComissionSetting::get();
        return response()->json([$settings]);
    }
    public function leadershipindex()
    {
        $settings= LeadershipSetting::get();
        return response()->json([$settings]);
    }
     public function convertindex()
    {
        $settings= ConvertSetting::get();
        return response()->json([$settings]);
    }
     public function convertindexUser()
    {
        $settings= ConvertSetting::get();
        return response()->json([$settings]);
    }
    
    public function update(Request $request)
    {
    
    $setting = ComissionSetting::where('id',$request->id)->first();
    $setting->refer_comission = $request->refer_comission;
    $setting->level_comission_1 = $request->level_comission_1;
    $setting->level_comission_2 = $request->level_comission_2;
    $setting->level_comission_3 = $request->level_comission_3;
    $setting->free_level_1 = $request->free_level_1;
    $setting->free_level_2 = $request->free_level_2;
    $setting->free_level_3 = $request->free_level_3;
    $setting->free_mining_rewards = $request->free_mining_rewards;
    $setting->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
    }
    
    public function leadershipsettingupdate(Request $request)
    {
        $setting = LeadershipSetting::where('id',$request->id)->first();
        $setting->free_direct_refer_1_qty = $request->free_direct_refer_1_qty;
        $setting->free_direct_refer_1_amount= $request->free_direct_refer_1_amount;
        $setting->free_direct_refer_2_qty = $request->free_direct_refer_2_qty;
        $setting->free_direct_refer_2_amount= $request->free_direct_refer_2_amount;
        $setting->free_direct_refer_3_qty = $request->free_direct_refer_3_qty;
        $setting->free_direct_refer_3_amount= $request->free_direct_refer_3_amount;
        $setting->free_direct_refer_4_qty = $request->free_direct_refer_4_qty;
        $setting->free_direct_refer_4_amount= $request->free_direct_refer_4_amount;
        $setting->free_direct_refer_5_qty = $request->free_direct_refer_5_qty;
        $setting->free_direct_refer_5_amount= $request->free_direct_refer_5_amount;
        
        $setting->free_team_member_1_qty = $request->free_team_member_1_qty;
        $setting->free_team_member_1_amount= $request->free_team_member_1_amount;
        $setting->free_team_member_2_qty = $request->free_team_member_2_qty;
        $setting->free_team_member_2_amount= $request->free_team_member_2_amount;
        $setting->free_team_member_3_qty = $request->free_team_member_3_qty;
        $setting->free_team_member_3_amount= $request->free_team_member_3_amount;
        $setting->free_team_member_4_qty = $request->free_team_member_4_qty;
        $setting->free_team_member_4_amount= $request->free_team_member_4_amount;
        $setting->free_team_member_5_qty = $request->free_team_member_5_qty;
        $setting->free_team_member_5_amount= $request->free_team_member_5_amount;
        
        $setting->paid_direct_refer_1_qty = $request->paid_direct_refer_1_qty;
        $setting->paid_direct_refer_1_amount= $request->paid_direct_refer_1_amount;
        $setting->paid_direct_refer_2_qty = $request->paid_direct_refer_2_qty;
        $setting->paid_direct_refer_2_amount= $request->paid_direct_refer_2_amount;
        $setting->paid_direct_refer_3_qty = $request->paid_direct_refer_3_qty;
        $setting->paid_direct_refer_3_amount= $request->paid_direct_refer_3_amount;
        $setting->paid_direct_refer_4_qty = $request->paid_direct_refer_4_qty;
        $setting->paid_direct_refer_4_amount= $request->paid_direct_refer_4_amount;
        $setting->paid_direct_refer_5_qty = $request->paid_direct_refer_5_qty;
        $setting->paid_direct_refer_5_amount= $request->paid_direct_refer_5_amount;
        
        $setting->paid_team_member_1_qty = $request->paid_team_member_1_qty;
        $setting->paid_team_member_1_amount= $request->paid_team_member_1_amount;
        $setting->paid_team_member_2_qty = $request->paid_team_member_2_qty;
        $setting->paid_team_member_2_amount= $request->paid_team_member_2_amount;
        $setting->paid_team_member_3_qty = $request->paid_team_member_3_qty;
        $setting->paid_team_member_3_amount= $request->paid_team_member_3_amount;
        $setting->paid_team_member_4_qty = $request->paid_team_member_4_qty;
        $setting->paid_team_member_4_amount= $request->paid_team_member_4_amount;
        $setting->paid_team_member_5_qty = $request->paid_team_member_5_qty;
        $setting->paid_team_member_5_amount= $request->paid_team_member_5_amount;
        
        
        $setting->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
        
        
    }
    public function updateconvertsetting(Request $request)
    {
    
    $setting = ConvertSetting::where('id',$request->id)->first();
    $setting->minimum_convert = $request->minimum_convert;
    $setting->maximum_convert = $request->maximum_convert;
    $setting->charge = $request->charge;
    $setting->duration = $request->duration;
    $setting->save();
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
    }
    public function popupsetting(Request $request)
    {
    $setting = PopupSetting::where('id',$request->id)->first();
    $setting->title = $request->title;
    $setting->description = $request->description;
    $setting->status = $request->status;
    $setting->link = $request->link;
    $setting->app_version = $request->app_version;
    $setting->save();
        
    }
}
