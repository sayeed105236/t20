<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BlockgumService;

class BlockgumController extends Controller
{
    protected $blockgumService;

    public function __construct(BlockgumService $blockgumService)
    {
        $this->blockgumService = $blockgumService;
    }

    public function createAddress(Request $request)
    {
        $uid = $request->input('uid');
       // $response = $this->blockgumService->createAddress($uid);
       $response = $this->blockgumService->getLatestDeposits($uid);
        return response()->json($response);
    }

}
