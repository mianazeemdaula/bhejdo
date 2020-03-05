<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ServiceCharge;

class ServiceChargesController extends Controller
{
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
