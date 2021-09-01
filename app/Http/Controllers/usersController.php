<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Response;

class usersController extends Controller
{
    public function users_info() {
        $users=DB::select(DB::raw("SELECT *
            FROM users
            where type != 'manager' and type != 'exective_manager'
            "));

            return response()->json([
                'message' => 'users found',
                'users' => $users,
            ], 201);
    }
}
