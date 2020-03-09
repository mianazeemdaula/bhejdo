<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Wallet;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        try{
            $wallet = Wallet::where('user_id',$request->user()->id)->latest('id')->limit(100)->get();
            $wallet = \App\Http\Resources\LedgerEntry::collection($wallet);
            return response()->json(['status'=>true, 'data' => $wallet], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }

    public function fetchaccount(Request $request)
    {
        try{
            $user = User::where('mobile', $id)->orWhere('reffer_id', $id)->first();
            return response()->json(['status'=>true, 'data' => $user], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }

    public function balance(Request $request)
    {
        try{
            $bonus = Wallet::balance($request->user()->id);
            return response()->json(['status'=>true, 'data' => $bonus], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }
}
