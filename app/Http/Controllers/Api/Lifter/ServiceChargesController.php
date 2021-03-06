<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ServiceCharge;

class ServiceChargesController extends Controller
{
    public function index(Request $request)
    {
        try{
            $charges = ServiceCharge::where('user_id',$request->user()->id)->latest('id')->limit(100)->get();
            $charges = \App\Http\Resources\LedgerEntry::collection($charges);
            return response()->json(['status'=>true, 'data' => $charges], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }

    public function balance(Request $request)
    {
        try{
            $bonus = ServiceCharge::balance($request->user()->id);
            return response()->json(['status'=>true, 'data' => $bonus], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }
}
