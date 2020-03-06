<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\User;

class ApiAuthController extends Controller
{
    
    public function accountStatus(Request $request) {
        try{
            return response()->json(['status'=>true, 'data' => ['user' => $request->user()]], 200);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
        }
    }

    public function phoneRegister(Request $request)
    {
        try{
            $user = User::where('mobile',$request->mobile)->first();
            if($user != null){
                return response()->json(['status'=>true], 200);
            }
            return response()->json(['status'=>false, 'data' => 'User not registered' ], 401);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
        }
    }

    public function phoneRegisterLifter(Request $request)
    {
        try{
            $user = User::where('mobile',$request->mobile)->first();
            if($user != null){
                if($user->hasRole('lifter')){
                    return response()->json(['status'=>true, 'isregister' => true], 200);
                }
                return response()->json(['status'=>true, 'isregister' => true, 'role' => false], 200);
            }
            return response()->json(['status'=>false, 'isregister' => false ], 200);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
        }
    }
}
