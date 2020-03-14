<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Bonus;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        try{
            $wallet = Bonus::where('user_id',$request->user()->id)->latest('id')->limit(100)->get();
            $wallet = \App\Http\Resources\LedgerEntry::collection($wallet);
            return response()->json(['status'=>true, 'data' => $wallet], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }

    public function balance(Request $request)
    {
        try{
            $bonus = Bonus::where("user_id", $request->user()->id)->latest('id')->first();
            return response()->json(['status'=>true, 'data' => $bonus], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }
}
