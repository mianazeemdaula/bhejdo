<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Illuminate
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

// Models
use DB;
use App\User;
use App\Bonus;
use App\ServiceCharge;

// Helpers
use App\Helpers\BonusProcess;
use App\Helpers\UserHelper;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = ['mobile' => $request->mobile, 'password' => $request->password];
        if(Auth::attempt($credentials))
        {
            $user = Auth::user();
            if($user->hasRole('consumer')){
                $success['user'] = new \App\Http\Resources\Profile\ConsumerProfile($user); 
                $success['token'] = $user->createToken($user->account_type)->accessToken; 
                return response()->json(['status'=>true, 'data' => $success], 200);
            }
            return response()->json(['error'=>'Not authorized to login'], 401);
        }
        else
        { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    }

    public function register(Request $request) {
        try{
            DB::beginTransaction();
            $validator = Validator::make( $request->all(), [
                'name' => 'required',
                'mobile' => 'required|min:11|max:11|unique:users',
                'email' => 'unique:users|email|nullable',
                'referred' => 'exists:users,mobile|nullable',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $user = new User();
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->address = $request->address;
            $user->account_type = 'consumer';
            $user->referred_by = $request->referred;
            $user->status = 'varified';
            $user->save();

            $profile = $user->profile()->create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude
            ]);
            
            $user->assignRole('consumer');
            Bonus::add($user->id,'Signup bonus','signup', 200);
            if($request->referred != null){
                $referrBy = User::where('mobile', $request->referred)->first();
                if($referrBy != null){
                    if($referrBy->hasRole('store|lifter')){
                        ServiceCharge::add($referrBy->id, "Referr bonus of {$user->name}", "referr", 100);
                    }else{
                        Bonus::add($referrBy->id,"Referr bonus of {$user->name}",'referr', 100);
                    }
                }
            }
            
            $success['token'] = $user->createToken($user->account_type)->accessToken;
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            $success['user'] = $profile;
            DB::commit();
            return response()->json(['status'=>true, 'data' => $success], 200);
        }catch(Expection $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function pushToken(Request $request) {
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            return response()->json(['status'=>'success'], 200);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
        }
    }

    public function updateForgetPassword(Request $request){
        DB::beginTransaction();
        try{
            
            $validator = Validator::make( $request->all(), [
                'mobile' => 'required|min:11|max:11',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $user = User::where('mobile', $request->mobile)->first();
            if($user == null){
                return response()->json(['status'=> false, 'data' => $request->all()], 405);
            }
            $user->password = bcrypt($request->password);
            $user->save();
            DB::commit();
            return response()->json(['status'=> true, 'data' => 'updated'], 200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['success'=>$e], 405);
        }
    }

    public function profile(Request $request)
    {
        try{
            $user = $request->user();
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            return response()->json(['status'=>true, 'data' => $profile], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }

    public function update(Request $request)
    {
        try{
            $type = strtolower($request->type);
            if($type == "avatar"){
                $avatar = $request->avatar;
                $avatarImageName = $request->user()->id.'_avatar.'.'png';
                \Storage::disk('public')->put($avatarImageName, base64_decode($avatar));

                $user = $request->user();
                $user->avatar = $avatarImageName;
                $user->save();
                return response()->json(['status'=>true, 'data' => $avatarImageName], 200);
            }
            return response()->json(['status'=>false, 'error' => "Update Type Error" ], 401);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Erro sdfsdfsd" ], 405);
        }
    }

    
}
