<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackageSetting;
use App\Models\PurchasePackage;

class PackageSettingController extends Controller
{
    public function index()
    {
        $packages = PackageSetting::select('package_settings.package_name as spinner_name','package_settings.package_price as spinner_price',
    'package_settings.duration as duration','package_settings.created_at as created_at'
    )->where('package_settings.is_deleted',0)->get();
        return response()->json(['spinners'=>$packages]);
    }
    
    public function store(Request $request)
    
    {
        $rules = [
       // 'name' => 'required|string',
        'spinner_price' => 'required|numeric',
        'duration' => 'required|integer',
      
    ];

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
     $image = $request->file('image');
    $filename = null;
    if ($image) {
        $filename = time() . $image->getClientOriginalName();

        Storage::disk('public')->putFileAs(
            'spinners/',
            $image,
            $filename
        );
    }
    $package = new PackageSetting();
    $package->package_name = 'BDT '.$validatedData['spinner_price'];
    $package->package_price = $validatedData['spinner_price'];
    $package->duration = $validatedData['duration'];
   
    $package->image= $filename;
    $package->save();

    $pack = PackageSetting::select('package_settings.package_name as spinner_name','package_settings.package_price as spinner_price',
    'package_settings.duration as duration','package_settings.created_at as created_at'
    )->where('package_settings.id',$package->id)->first();
     
    // Return the saved data as JSON response
    return response()->json(['spinner' => $pack]);
        
    }
    public function update(Request $request)
    
    {
        $rules = [
            // 'name' => 'required|string',
             'spinner_price' => 'required|numeric',
             'duration' => 'required|integer',
           
         ];
     if($request->file('uimage') != null)
    {
      $image =$request->file('file');
      $filename=null;
      $uploadedFile = $request->file('image');
      $oldfilename = $package['image'] ?? 'demo.jpg';

      $oldfileexists = Storage::disk('public')->exists('spinners/' . $oldfilename);

      if ($uploadedFile !== null) {

          if ($oldfileexists && $oldfilename != $uploadedFile) {
              //Delete old file
              Storage::disk('public')->delete('packages/' . $oldfilename);
          }
          $filename_modified = str_replace(' ', '_', $uploadedFile->getClientOriginalName());
          $filename = time() . '_' . $filename_modified;

          Storage::disk('public')->putFileAs(
              'spinners/',
              $uploadedFile,
              $filename
          );

          $data['uimage'] = $filename;
       } elseif (empty($oldfileexists)) {
          // throw new \Exception('Client image not found!');
          $uploadedFile = null;
          
          return response()->json(['failed' => 400]);
          //file check in storage
        }
    }

    // Validate the request
    $validatedData = $request->validate($rules);

    // Create a new PakageSetting instance and save it
    $package = PackageSetting::where('id',$request->id)->first();
     if ($request->file('image') != null) {
        $package->image= $filename;
      }
      $package->package_name = 'BDT '.$validatedData['spinner_price'];
      $package->package_price = $validatedData['spinner_price'];
      $package->duration = $validatedData['duration'];
     
      //$package->image= $filename;
      $package->save();
       
        $package->save();
       
     
    // Return the saved data as JSON response
    return response()->json(['success' => 200]);
        
    }
    
    public function delete($id)
    {
        $package= PackageSetting::find($id);
        $package->is_deleted = 1;
        $package->save();
        return response()->json(['success' => 200]);
        
    }
    public function PurchaseHistory()
    {
        $purchases= PurchasePackage::select('purchase_packages.created_at as date','users.name','users.email','package_settings.*','purchase_packages.status as availibility')
         ->join('users','users.id','purchase_packages.user_id')
         ->join('package_settings','package_settings.id','purchase_packages.package_id')
         //->where('purchase_packages.user_id',Auth::id())
         ->get();
       
         return response()->json([
             'purchase_history'=> $purchases]);
    }
}
