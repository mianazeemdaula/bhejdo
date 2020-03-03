<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Bonus;

class BonusController extends Controller
{
    public function show(Request $request)
    {
        try{
            $bonus = Bonus::where("user_id", $request->user()->id)->latest('id')->first();
            return response()->json(['status'=>true, 'data' => $bonus], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }
}
