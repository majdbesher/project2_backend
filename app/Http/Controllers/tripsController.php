<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\trip;
use Validator;
use App\Http\Controllers\Controller;
use Response;

class tripsController extends Controller
{
    public function add_new_trip (Request $request)
        {
           $validator =Validator::make($request->all(),[
           'seats'=>'required',
           'date'=>'required',
           'hour'=>'required',
           'starting_point'=>'required',
           'distination_point'=>'required',
           'state'=>'required',
           'type'=>'required',
           ]);

           if($validator->fails())
           {
              return Response::json($validator->errors());
           }

           $tripp = trip::create(array_merge(
                               $request->all(),
                           ));

                   return response()->json([
                       'message' => 'trip successfully added',
                       'trip' => $tripp
                   ], 201);
        }

        public function check_added_trips (Request $request)
        {  
            $trips = DB::select(DB::raw("SELECT *
                   FROM trips
                   where date = :date2 and type='added'
                   "),
                       array('date2' => $request->date)
                   );   

            return response()->json([
                'message' => 'trips found',
                'trip' => $trips
            ], 201);
        }

        public function check_trips (Request $request)
        { 
            $trips = DB::select(DB::raw("SELECT *
                   FROM trips
                   where date = :date2 
                   "),
                       array('date2' => $request->date)
                   );   

                   for($i=0;$i<count($trips);$i++)
                   {
                   $res[$i] = DB::select(DB::raw("SELECT id
                   FROM reservations
                   where trip_id =:id and res_state='not approved'
                   "),
                       array('id' => $trips[$i]->id)
                   );
                }

            return response()->json([
                'message' => 'trips found',
                'trip' => $trips,
                'res' => $res
            ], 201);
        }

        public function get_s_trips (Request $request)
        { 
            $trips = DB::select(DB::raw("SELECT *
                   FROM trips
                   where type = 'scheduled'
                   "));   
            if(count($trips)==0)
            {
                return response()->json([
                    'message' => 'no trips found',
                    //'trip' => $trips,
                    //'res' => $res
                ], 201);
            }
            else{
                return response()->json([
                'message' => 'trips found',
                'trip' => $trips,
                //'res' => $res
                ], 201);
        }
        }
}
