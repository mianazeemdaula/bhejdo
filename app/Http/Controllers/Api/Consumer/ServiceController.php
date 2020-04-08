<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service;
use App\Http\Resources\ServiceView;

class ServiceController extends Controller
{
    public function getActive(Request $request)
    {
        try{
            $services = Service::where('s_status', 'active')->get();
            $services = ServiceView::collection($services);
            $smaple = \App\Order::where('type',3)->where('consumer_id', $request->user()->id)->count();
            return response()->json(['status'=>true, 'data' => $services, 'sample' => $smaple], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
