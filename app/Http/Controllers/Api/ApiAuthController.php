<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class ApiAuthController extends Controller
{
    public function login(Request $request){
        $credentials = ['mobile' => $request->mobile, 'password' => $request->password];
        if(Auth::attempt($credentials))
        { 
            $user = Auth::user();
            $success['user'] = $user; 
            $success['token'] = $user->createToken($user->account_type)->accessToken; 
            return response()->json(['success' => $success], 200);
        }
        else
        { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    } 

    public function registerConsumer(Request $request) {
        try{
            $validator = Validator::make( $request->all(), [
                'name' => 'required',
                'mobile' => 'required|min:11|max:11|unique:users',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            
            $user = new User();
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->password = bcrypt($request->password);
            $user->address = $request->address;
            $user->longitude = $request->longitude;
            $user->latitude = $request->latitude;
            $user->account_type = 'consumer';
            $user->save();
            $role = Role::firstOrCreate(['name' => 'consumer']);
            $user->assignRole('consumer');
            $success['token'] = $user->createToken($user->account_type)->accessToken;
            $success['user'] = $user;
            return response()->json(['status'=>true, 'data' => $success], 200);
        }catch(Expection $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function updatePushToken(Request $request) {
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            return response()->json(['status'=>'success'], 200);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
        }
    }
}
