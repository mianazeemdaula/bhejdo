<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        try{
            $subscribe = $request->user()->services->pluk('id');
            $services = Service::where('status', 'active')->all();
            $data = ['subscribe' => $services, 'services' => $services];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
