<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('users.name', 'users.email', 'users.phone', 'users.balance', 'users.password')
        ->where('users.is_admin', 0)
        ->where('users.is_verified', 1)
        ->get();

        foreach ($users as $user) {
        $user->password = Crypt::decryptString($user->password);
        $user->makeVisible('password');
        }

return response()->json(['users' => $users]);
        
    }
}
