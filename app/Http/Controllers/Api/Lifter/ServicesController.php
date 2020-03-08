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
            $subscribe = $request->user()->services->pluck('id')->toArray();
            $services = Service::where('s_status', 'active')->get();
            $data = ['subscribe' => $subscribe, 'services' => $services];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function store(Request $request)
    {
        return response()->json(['status'=>true, 'data' => $request->all()], 200);
    }
}
