<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use JWTFactory;
use JWTAuth;
use Validator;
use Response;

class APIRegController extends Controller
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


    public function register (Request $request)
    {
       $validator =Validator::make($request->all(),[
       'email'=>'required',
       'name'=>'required',
       'password'=>'required',
       'phone'=>'required',
       ]);

       if($validator->fails())
       {
           return Response::json($validator->errors());
       }

       $user = User::create(array_merge(
                           $validator->validated(),
                           ['password' => $request->password]
                       ));

               return response()->json([
                   'message' => 'User successfully registered',
                   'user' => $user
               ], 201);

       /*User::create([
              'email'=> $request->get('email'),
              'name'=>$request->get('name'),
              'password'=>$request->get('password'),
              'phone'=>$request->get('phone')
       ]);

       $user=User::first();
       $token=JWTAuth::fromUser($user);
       return Response::json(compact('token'));*/
    }
}
