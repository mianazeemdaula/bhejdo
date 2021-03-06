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
            if($user->hasRole('consumer|advertiser')){
                $success['user'] = new \App\Http\Resources\Profile\ConsumerProfile($user); 
                $success['token'] = $user->createToken($user->account_type)->accessToken; 
                $settings = config('ohyes.consumer');
                return response()->json(['status'=>true, 'data' => $success, 'settings' => $settings], 200);
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
            $user->status = 'verified';
            $user->save();

            $profile = $user->profile()->create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude
            ]);
            
            $user->assignRole('consumer');
            Bonus::add($user->id,'Signup bonus','signup', 100);
            if($request->referred != null){
                $referrBy = User::where('mobile', $request->referred)->first();
                if($referrBy != null){
                    if($referrBy->hasRole('store|lifter')){
                        ServiceCharge::add($referrBy->id, "You have reffered to Mr {$user->name}. He is added in your referral list after starts of his shopping you would get bonus", "referr", 0);
                    }else{
                        Bonus::add($referrBy->id,"You have reffered to Mr {$user->name}. He is added in your referral list after starts of his shopping you would get bonus",'referr', 0);
                    }
                }
            }
            
            $success['token'] = $user->createToken($user->account_type)->accessToken;
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            $success['user'] = $profile;
            DB::commit();
            \App\Jobs\SendSmsJob::dispatch("0300410310","New signup {$user->name}",'oyapi')->delay(now()->addSeconds(5));
            $settings = config('ohyes.consumer');
            return response()->json(['status'=>true, 'data' => $success, 'settings' => $settings], 200);
        }catch(Expection $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = User::find($request->user()-id);
            $user->pushToken = null;
            $user->save();
            return response()->json(['status'=>true ], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }

    public function pushToken(Request $request) {
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            $bonus = Bonus::balance($request->user()->id);
            return response()->json(['status'=>'success', 'bonus' => $bonus], 200);
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
            $settings = config('ohyes.consumer');
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            return response()->json(['status'=>true, 'data' => $profile, 'settings' => $settings], 200);
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
                $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
                return response()->json(['status'=>true, 'data' => $avatarImageName, 'profile' => $profile], 200);
            }
            return response()->json(['status'=>false, 'error' => "Update Type Error" ], 401);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Erro sdfsdfsd" ], 405);
        }
    }

    
}
