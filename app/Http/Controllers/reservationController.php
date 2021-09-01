<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\reservation;
use Validator;
use App\Http\Controllers\Controller;
use Response;

class reservationController extends Controller
{
        public function res_at_trip (Request $request)
        {  
            $id=DB::select(DB::raw("SELECT id
            FROM trips
            where hour = :hour2 and date=:date2
            "),
                array('hour2' => $request->hour,'date2' => $request->date)
            );

                   $ress = DB::select(DB::raw("SELECT seat_number
                   FROM reservations
                   where trip_id = (SELECT id
            FROM trips
            where hour = :hour2 and date=:date2)
                   "),
                       array('hour2' => $request->hour,'date2' => $request->date)
                   );    
            return response()->json([
                'message' => 'reservations found',
                'trip' => $ress,
                'id'=> $id
            ], 201);
        }
        
        public function reserve (Request $request)
        {
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
                'user_phone' => 'required',
                'seat_number' => 'required',
                'res_state' => 'required',
            ]);
    
            if($validator->fails())
           {
              return Response::json($validator->errors());
           }

           $res = reservation::create(array_merge(
                               $request->all(),
                           ));
            
                           return response()->json([
                            'message' => 'reserved successfully',
                            'res' => $res
                        ], 201);
        }

        public function not_approved_res_at_trip (Request $request)
        {  
            $id=DB::select(DB::raw("SELECT id
            FROM trips
            where hour = :hour2 and date=:date2
            "),
                array('hour2' => $request->hour,'date2' => $request->date)
            );

                   $ress = DB::select(DB::raw("SELECT id
                   FROM reservations
                   where trip_id = (SELECT id
            FROM trips
            where hour = :hour2 and date=:date2 and res_state='not approved')
                   "),
                       array('hour2' => $request->hour,'date2' => $request->date)
                   );

                   $numbers = DB::select(DB::raw("SELECT user_phone
                   FROM reservations
                   where trip_id = (SELECT id
            FROM trips
            where hour = :hour2 and date=:date2 and res_state='not approved')
                   "),
                       array('hour2' => $request->hour,'date2' => $request->date)
                   );
   
            return response()->json([
                'message' => 'reservations found',
                'trip' => $ress,
                'id'=> $id,
                'numbers' => $numbers
            ], 201);
        }

        public function confirm_res (Request $request)
        {  
            $number=DB::select(DB::raw("SELECT phone
            FROM users
            where idnumber = :id_number
            "),
                array('id_number' => $request->id_number)
            );

            reservation::where('trip_id', $request->trip_id)
            ->where('user_phone', $number[0]->phone)
            ->update(['res_state' => 'approved']);
   
            return response()->json([
                'message' => 'reservation approved',
            ], 201);
        }

        public function customer_res (Request $request)
        {  
            $res=DB::select(DB::raw("SELECT trip_id
            FROM reservations
            where user_phone = :phone and res_state='not approved'
            "),
                array('phone' => $request->phone)
            );
            if(count($res)!=0)
            {
                $trip_ids=array();
                for($i=0;$i<count($res);$i++)
                {
                    if(!in_array($res[$i]->trip_id,$trip_ids))
                    {
                        array_push($trip_ids, $res[$i]->trip_id);
                    }
                }
                $trips=array();
                for($i=0;$i<count($trip_ids);$i++)
                {
                    $trip=DB::select(DB::raw("SELECT *
                        FROM trips
                        where id = :id2
                    "),
                      array('id2' => $trip_ids[$i])
                    );
                    array_push($trips, $trip);
                }

                return response()->json([
                    'message' => 'reservation found',
                    'trips' => $trips
                ], 201);
            }
            else
            {
                return response()->json([
                    'message' => 'no reservation found',
    
                ], 201);
            }

        }

        public function reserved_seats (Request $request)
        {  
            $seats=DB::select(DB::raw("SELECT seat_number
            FROM reservations
            where trip_id = :id and user_phone =:phone and res_state='not approved'
            "),
                array('id' => $request->id, 'phone'=>$request->phone)
            );
   
            return response()->json([
                'message' => 'reservation approved',
                'seats' => $seats
            ], 201);
        }

        public function del_reserve (Request $request)
        {  
            $deleted=DB::delete(DB::raw("Delete FROM reservations
            where trip_id = :id and user_phone =:phone and res_state='not approved'
            "),
                array('id' => $request->trip_id, 'phone'=>$request->user_phone)
            );

            return response()->json([
                'message' => 'reservation deleted',
                'reservation' => $deleted
            ], 201);
        }
}
