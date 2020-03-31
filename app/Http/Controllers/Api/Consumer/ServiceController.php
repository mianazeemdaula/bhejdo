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
            return response()->json(['status'=>true, 'data' => $services], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
