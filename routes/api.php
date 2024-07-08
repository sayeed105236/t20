<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\PackageSettingController;
use App\Http\Controllers\Api\Admin\ComissionSettingController;
use App\Http\Controllers\Api\Admin\WithdrawSettingController;
use App\Http\Controllers\Api\Admin\TransferSettingController;
use App\Http\Controllers\Api\Admin\AdminWalletController;
use App\Http\Controllers\Api\Admin\VoucherController;
use App\Http\Controllers\Api\User\BizWalletController;
use App\Http\Controllers\Api\User\UsdtWalletController;
use App\Http\Controllers\Api\User\UserWalletController;
use App\Http\Controllers\Api\User\BlockgumController;
use App\Http\Controllers\Api\Admin\UsdtTokenRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'register']);

Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
Route::get('/password-reset', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password-reset-store', [AuthController::class, 'resetStore']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/referrer-check', [AuthController::class, 'CheckRefer']);
 Route::get('/user/approve_fund/{amount}/{description}/{user}', [UsdtWalletController::class, 'approveFund']);
 Route::post('/blockgum/create-address', [BlockgumController::class, 'createAddress']);
 Route::get('/popup-details', [AuthController::class, 'popup']);




Route::middleware('auth:api')->group(function(){
    //send otp 
    Route::get('/send-otp', [AuthController::class, 'OtpSend']);
    //referrer lists
    Route::get('/referrer-lists', [AuthController::class, 'sponsor_list']);
    Route::post('/verified',[AuthController::class,'verifiedOtp']);
    
    //user biz wallet balance 
    
    Route::get('/balances', [UserWalletController::class, 'balances']);
    Route::get('/user-levels', [UserWalletController::class, 'UserLevel']);
    Route::get('/biz-wallet', [BizWalletController::class, 'index']);
      Route::get('/usdt-wallet', [UsdtWalletController::class, 'index']);
     //user biz wallet deposit
      Route::get('/user-admin-wallets', [UsdtWalletController::class, 'AdminWallet']);
      Route::post('/usdt-wallet/add-money', [UsdtWalletController::class, 'AddMoney']);
     // Route::post('/usdt-wallet/add-money', [UsdtWalletController::class, 'autostore']);
      Route::get('/user-spinners', [UsdtWalletController::class, 'packages']);
      Route::get('/user/convert-setting', [ComissionSettingController::class, 'convertindexUser']);
      
      Route::post('/user/add-fund/store', [UsdtWalletController::class,'autostore']);
    
    Route::get('/user/add-fund/cancel', [UsdtWalletController::class,'cancel']);
      Route::get('/user/add-fund/binance', [UsdtWalletController::class,'initiateBinancePay']);
       
      
      Route::get('/user/comission-setting', [ComissionSettingController::class, 'indexUser']);
      
      //user usdt transfer
      Route::post('/usdt-wallet/transfer-money', [UsdtWalletController::class, 'TransferMoney']);
      //purchase packages
      Route::get('/package-purchase-history', [UsdtWalletController::class, 'PurchaseHistory']);
      Route::get('/user/deposit-history', [UserWalletController::class, 'DepositHistory']);
      Route::get('/user/withdraw-history', [UserWalletController::class, 'WithdrawHistory']);
      Route::get('/user/transfer-history', [UserWalletController::class, 'TransferHistory']);
      Route::get('/user/refer-history', [UserWalletController::class, 'ReferHistory']);
      Route::get('/user/level-history', [UserWalletController::class, 'LevelHistory']);
      Route::get('/user/leadership-history', [UserWalletController::class, 'LeadershipHistory']);
      Route::get('/user/free-mining-history', [UserWalletController::class, 'FreeMiningHistory']);
      Route::get('/user/package-mining-history', [UserWalletController::class, 'PackageMiningHistory']);
      Route::get('/user/a2i-token-history', [UserWalletController::class, 'A2ITokenHistory']);
     
      Route::get('/user/free-level-history', [UserWalletController::class, 'FreelevelHistory']);
      Route::post('/purchase-spinner', [UsdtWalletController::class, 'PurchasePackage']);
      
      Route::get('/voucher-purchase-history', [UsdtWalletController::class, 'VoucherPurchaseHistory']);
      Route::post('/purchase-voucher', [UsdtWalletController::class, 'PurchaseVoucher']);
      Route::get('/user-deposit-check', [UserWalletController::class, 'DepositCheck']);
      
      
      //user wallets
     Route::post('/user-wallet/store', [UserWalletController::class, 'store']);
    Route::get('/user-wallets', [UserWalletController::class, 'index']);
    Route::post('/user-wallet/update', [UserWalletController::class, 'update']);
    Route::get('/user-wallet/delete/{id}', [UserWalletController::class, 'delete']);
    //biz wallet withdrawals 
    Route::post('/biz-wallet/withdraw-money', [BizWalletController::class, 'WithdrawMoney']);
    //free mining
    Route::get('/user-free-mining', [BizWalletController::class, 'FreeMiningCheck']);
    Route::get('/user-free-mining/claim', [BizWalletController::class, 'ClaimFreeMining']);
    Route::get('/user-package-mining', [BizWalletController::class, 'PackageMiningCheck']);
    Route::get('/user-package-mining/start', [BizWalletController::class, 'StartMining']);
    Route::get('/usdt-wallet/history', [UserWalletController::class, 'UsdtHistory']);
    Route::get('/biz-wallet/history', [UserWalletController::class, 'BizHistory']);
    Route::get('/a2i-wallet/history', [UserWalletController::class, 'A2IHistory']);
    Route::get('/bizt-wallet/history', [UserWalletController::class, 'BizTHistory']);
     // Biz to BizT conversion 
     Route::get('/user-biz-converstion', [BizWalletController::class, 'ConversionCheck']);
     Route::post('/biz-wallet/convertbiz', [BizWalletController::class, 'convertbiz']);
     //user voucher list 
       Route::get('/user/vouchers', [VoucherController::class, 'indexUser']);
    //user profile 
     Route::get('/user-profile', [AuthController::class, 'UserProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    Route::post('/logout',[AuthController::class,'logout']);
});

//admin routes

//user list api
Route::middleware(['auth:api','admin'])->group(function(){
    Route::get('/comission-setting', [ComissionSettingController::class, 'index']);
    Route::post('/comission-setting/update', [ComissionSettingController::class, 'update']);
     Route::get('/leadership-setting', [ComissionSettingController::class, 'leadershipindex']);
    Route::post('/leadership-setting/update', [ComissionSettingController::class, 'leadershipsettingupdate']);
     Route::get('/convert-setting', [ComissionSettingController::class, 'convertindex']);
    Route::post('/convert-setting/update', [ComissionSettingController::class, 'updateconvertsetting']);
    Route::post('/popup-setting/update', [ComissionSettingController::class, 'popupsetting']);
    
    //user usdt token add request
     Route::get('/usdt-add-request', [UsdtTokenRequestController::class, 'index']);
     Route::post('/usdt-add-request/approve', [UsdtTokenRequestController::class, 'Approve']);
     
     
     
     //user biztoken withdrawal request 
     Route::get('/biztoken-withdraw-request', [UsdtTokenRequestController::class, 'WithdrawRequest']);
     Route::post('/biztoken-withdraw-request/approve', [UsdtTokenRequestController::class, 'WithdrawApprove']);
     
     //withdraw settings
    Route::get('/withdrawal-setting', [WithdrawSettingController::class, 'index']);
    Route::post('/withdrwal-setting/update', [WithdrawSettingController::class, 'update']);
     //transfer settings
    Route::get('/transfer-setting', [TransferSettingController::class, 'index']);
    Route::post('/transfer-setting/update', [TransferSettingController::class, 'update']);
    //admin wallet settings 
    Route::post('/admin-wallet/store', [AdminWalletController::class, 'store']);
    Route::get('/admin-wallets', [AdminWalletController::class, 'index']);
    Route::post('/admin-wallet/update', [AdminWalletController::class, 'update']);
    Route::get('/admin-wallet/delete/{id}', [AdminWalletController::class, 'delete']);
    
    Route::get('/user-lists', [UserController::class, 'index']);
    //admin package setings
    Route::post('/spinner/store', [PackageSettingController::class, 'store']);
    Route::get('/spinners', [PackageSettingController::class, 'index']);
    Route::post('/spinner/update', [PackageSettingController::class, 'update']);
    Route::get('/spinner/delete/{package_id}', [PackageSettingController::class, 'delete']);
     Route::get('/admin/spinner-purchase-history', [PackageSettingController::class, 'PurchaseHistory']);
     
     //admin voucher settings 
     Route::post('/voucher/store', [VoucherController::class, 'store']);
    Route::get('/vouchers', [VoucherController::class, 'index']);
    Route::post('/voucher/update', [VoucherController::class, 'update']);
    Route::get('/voucher/delete/{voucher_id}', [VoucherController::class, 'delete']);

    //admin add balance 
    Route::post('/admin-add-balance', [AuthController::class, 'addBalance']);
    Route::post('/admin/user-update', [AuthController::class, 'UserUpdate']);

});
