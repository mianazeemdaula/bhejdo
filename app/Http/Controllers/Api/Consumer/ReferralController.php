<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        try{
            $referrals = User::where('referred_by',$request->user()->mobile)->latest('id')->get();
            $referrals = \App\Http\Resources\V2\Consumer\ReferralResource::collection($referrals);
            return response()->json(['status'=>true, 'data' => $referrals], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
