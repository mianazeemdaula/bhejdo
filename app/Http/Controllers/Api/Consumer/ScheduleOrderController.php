<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ScheduleOrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            return response()->json(['status'=>true, 'data' => $request->all() ], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }
}
