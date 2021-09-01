<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use JWTFactory;
use JWTAuth;
use Validator;
use Response;

class APISginInController extends Controller
{
    public function __construct() {
           $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    protected function createNewToken($token){
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user()
            ]);
        }

        public function userProfile() {
                return response()->json(auth()->user());
            }

        public function refresh() {
                return $this->createNewToken(auth()->refresh());
            }

    public function sginin (Request $request)
        {
           $validator = Validator::make($request->all(), [
                       //'email' => 'required',
                       'password' => 'required',
                       'phone' => 'required',
                       //'name' => 'required',
                   ]);

                   if ($validator->fails()) {
                       return response()->json($validator->errors(), 422);
                   }

                   if (! $token = auth()->attempt($validator->validated())) {
                       return response()->json(['error' => 'Unauthorized'], 401);
                   }
                   //return response()->json([
                   //                   'message' => 'User successfully signed in',
                   //                   'token' => $this->createNewToken($token)
                   //                   'user' => $user
                   //               ], 201);*/
                   return $this->createNewToken($token);
}
}
