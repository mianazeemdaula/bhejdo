<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\OpenOrder;

class OpenOrderController extends Controller
{
    public function show($orderid)
    {
        try{
            $order = OpenOrder::with(['consumer','service'])->where('id',$orderid)->first();
            if($order == null){
                return response()->json(['status'=>false, 'data' => ['Order assigned to anotherone']], 200);
            }
            return response()->json(['status'=>true, 'data' => $order], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
