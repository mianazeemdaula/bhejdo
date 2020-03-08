<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service;
use DB;

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
        try{
            $updated = json_decode($request->services);
            foreach($user->services as $service){
                if(in_array($service->id,$updated)){
                    $user->services()->updateExistingPivot($service->id, ['status' => 1]);
                    $key = array_search($service->id,$updated);
                    unset($updated[$key]);
                }else{
                    $user->services()->updateExistingPivot($service->id, ['status' => 0]);   
                }
            }
            foreach($updated as $u){
                $level = Level::where('service_id',$u)->first();
                $user->services()->attach($u, ['status' => 1, 'level_id' => $level->id]);
            }
            return response()->json(['status'=>true, 'data' => 'updated' ], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
