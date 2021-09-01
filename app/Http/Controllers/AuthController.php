<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

use App\Http\Controllers\Controller;
use JWTFactory;
use JWTAuth;
use Response;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

    	$validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $token = JWTAuth::attempt($validator->validated());
        if (!$token ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token=$this->createNewToken($token);
        $user=$this->userProfile();

        //dump($user);

        return response()->json([
                    'message' => 'User successfully signed in',
                    'user' => $user,
                    'token'=> $token
                ], 201);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'idnumber' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'type' => 'required',
            'university_name',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
                $token = JWTAuth::attempt($validator->validated());
                if ($token ) {
                $token=$this->createNewToken($token);
                }
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token'=> $token
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(JWTAuth::user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            //'expires_in' => auth('api')->factory()->getTTL,
            'expires_in' => JWTFactory::getTTL() * 60,
            'user' => JWTAuth::user()
        ]);
    }

}
