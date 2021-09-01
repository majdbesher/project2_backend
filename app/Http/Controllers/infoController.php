<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\info;
use Validator;
use App\Http\Controllers\Controller;
use Response;

class infoController extends Controller
{
    public function provide_info (Request $request)
        {
           $validator =Validator::make($request->all(),[
           'user_phone'=>'required',
           'end_hour'=>'required',
           'start_point'=>'required',
           ]);

           if($validator->fails())
           {
              return Response::json($validator->errors());
           }

           $info = info::create(array_merge(
                               $request->all(),
                           ));

                   return response()->json([
                       'message' => 'info provided successfully',
                       'trip' => $info
                   ], 201);
            
        }
        
        public function check_info (Request $request)
        {
            $trips = DB::select(DB::raw("SELECT *
                   FROM infos
                   where user_phone = :user_phone2 
                   "),
                       array('user_phone2' => $request->phone)
                   );

            if(count($trips)!=0)
            {
                return response()->json([
                    'message' => 'info already provided',
                ], 202);
            }
            else{
                return response()->json([
                    'message' => 'you can provided info',
                ], 202);
            }
        }
}
