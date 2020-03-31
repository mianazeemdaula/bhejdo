<?php

namespace App\Http\Controllers\Api\Lifter;

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
use App\LifterLocation;
use App\Review;
use App\ServiceCharge;

// Helpers
use App\Helpers\BonusProcess;
use App\Helpers\UserHelper;
use App\Helpers\AndroidNotifications;


class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = ['mobile' => $request->mobile, 'password' => $request->password];
        if(Auth::attempt($credentials))
        { 
            $user = Auth::user();
            if($user->hasRole('lifter|store')){
                $response['user'] = new \App\Http\Resources\Profile\LifterProfile($user); 
                $response['token'] = $user->createToken($user->account_type)->accessToken; 
                return response()->json(['status'=>true, 'data' => $response], 200);
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
                'password' => 'required',
                'referred' => 'exists:users,mobile|nullable',
                'confirm_password' => 'required|same:password',
                'type' => ['required', Rule::in(['lifter', 'store'])]
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            if($request->referred != null){
                if($request->type == 'store'){
                    return response()->json(['error'=>['referred' => ["Store account can't be referred."]]], 401);
                }
                $referrBy = User::where('mobile', $request->referred)->first();
                if($referrBy->hasRole('store') && $request->type == 'lifter'){

                }else{
                    return response()->json(['error'=>['referred' => ["Referral id is not a store."]]], 401);
                }
            }
            
            $user = new User();
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->address = $request->address;
            $user->account_type = $request->type;
            $user->referred_by = $request->referred;
            $user->status = 'unvarified';
            $user->save();
            $profile = $user->profile()->create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude
            ]);

            if($request->referred != null){
                $referrBy = User::where('mobile', $request->referred)->first();
                if($referrBy != null){
                    ServiceCharge::add($referrBy->id,"Referr bonus of {$user->name}",'referr', 100);
                }
            }

            $user->assignRole($user->account_type);
            $user->services()->attach([1 => ['level_id' => 1, 'status'=> 1]]);
            // $user->services()->attach([2 => ['level_id' => 5, 'status'=> 1]]);
            // $user->services()->attach([3 => ['level_id' => 9, 'status'=> 1]]);
            // $user->services()->attach([4 => ['level_id' => 13, 'status'=> 1]]);

            if($user->hasRole('store')){
                ServiceCharge::add($user->id, "Signup bonus for fee", "bonus", 1000);
            }else if($user->hasRole('lifter')){
                ServiceCharge::add($user->id, "Signup bonus for fee", "bonus", 500);
            }
            $response['token'] = $user->createToken($user->account_type)->accessToken;
            $response['user'] = new \App\Http\Resources\Profile\LifterProfile($user);
            DB::commit();
            return response()->json(['status'=>true, 'data' => $response], 200);
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

    public function accountStatus(Request $request) {
        try{
            $user = new \App\Http\Resources\Profile\LifterProfile($request->user());
            return response()->json(['status'=>true, 'data' => ['user' => $user]], 200);
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

    public function pushToken(Request $request) {
        DB::beginTransaction();
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            $lifter = LifterLocation::where('lifter_id',$request->user()->id)->first();
            $location = Array();
            $location =  [
                'type' => 'Point',
                'coordinates' => [$user->profile->latitude,$user->profile->longitude]
            ];
            $data = [
                'name' => $user->name,
                'avatar' => $user->avatar,
                'account_type' => $user->getRoleNames()[0],
                'services' => $user->services()->wherePivot('status',1)->pluck('id')->toArray(),
                'services_details' => [], 
                'last_update' => Carbon::now()->timestamp,
                'lifter_id' => $request->user()->id,
                // 'onwork' => "0",
                'location' => $location
            ];
            if($lifter == null){
                $lifter = LifterLocation::create($data);
            }else{
                $lifter->update($data);
            }
            $lifter->unset('services_details');
            $scoreData = [];
            foreach($user->services as $service){
                $ids = $service->orders()->where('status','confirmed')->pluck('id')->toArray();
                $rate = Review::whereIn('order_id', $ids)->avg('starts');
                $rate = $rate == null ? 0 : $rate;
                $data = ['orders' => count($ids), 'rate' => $rate];
                
                $scoreData[$service->id] = $data;
            }
            $lifter->push('services_details', $scoreData, true);
            DB::commit();
            return response()->json(['status'=> true, 'data' => $lifter], 200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['success'=>$e], 405);
        }
    }

    public function nicVerificaiton(Request $request)
    {
        try{
            $nicFront = $request->nicFront;
            $frontImageName = $request->user()->id.'_cnic_front.'.'png';
            \Storage::disk('public')->put($frontImageName, base64_decode($nicFront));

            $nicBack = $request->nicBack;
            $backImageName = $request->user()->id.'_cnic_back.'.'png';
            \Storage::disk('public')->put($backImageName, base64_decode($nicBack));

            $user = User::findOrFail($request->user()->id);
            $user->profile()->update([
                'cnic_front' => $frontImageName,
                'cnic_back' => $backImageName
            ]);
            $user->status = 'inprocess';
            $user->save();
            return response()->json(['status'=>true, 'data' => ['user' => new \App\Http\Resources\Profile\LifterProfile($user)]], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }

    public function profile(Request $request)
    {
        try{
            $user = $request->user();
            $profile = new \App\Http\Resources\Profile\LifterProfile($user);
            return response()->json(['status'=>true, 'data' => $profile], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }
    

    public function updateWorkingLocation(Request $request)
    {
        try {
            $profile = $request->user()->profile->update([
                'latitude' => $request->lat,
                'longitude' => $request->lon,
            ]);
            $location =  [
                'type' => 'Point',
                'coordinates' => [$request->lat,$request->lon]
            ];
            LifterLocation::where('lifter_id',$request->user()->id)->update(['location'=> $location,'last_update' => Carbon::now()->timestamp ]);
            return response()->json(['status'=>true, 'data' => "Updated Successfully", 'latlng' => $profile], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }

    public function onwork(Request $request)
    {
        try {
            $profile = $request->user()->profile;
            if($profile->latitude == 0.0 || $profile->longitude == 0.0){
                return response()->json(['status'=>false, 'onwork' => $request->onwork, 'data' => "Update your working location in profile" ], 200);
            }
            LifterLocation::where('lifter_id',$request->user()->id)->update(['onwork'=> $request->onwork]);
            $lifter = LifterLocation::where('lifter_id',$request->user()->id)->first();
            return response()->json(['status'=>true, 'onwork' => $request->onwork, 'data' => $lifter ], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }

    
    
    public function update(Request $request)
    {
        try{
            $type = strtolower($request->type);
            if($type == "avatar"){
                // if($reffer->hasFile('avatar')){
                //     $request->validate([
                //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                //     ]);
                //     $imageName = "profile_".$request->user()->id.".".$request->avatar->getClientOriginalExtension();
                //     $request->avatar->move(public_path('avatars'), $imageName);
                //     $user = $request->user();
                //     $user->avatar = $imageName;
                //     $user->save();
                //     return response()->json(['status'=>true, 'data' => "$imageName"], 200);
                // }
                // return response()->json(['status'=>true, 'data' => "file not found"], 401);
                $avatar = $request->avatar;
                $avatarImageName = $request->user()->id.'_avatar.'.'png';
                \Storage::disk('public')->put($avatarImageName, base64_decode($avatar));

                $user = $request->user();
                $user->avatar = $avatarImageName;
                $user->save();
                return response()->json(['status'=>true, 'data' => $avatarImageName], 200);
            }else if($type == "bikenumber"){
                $profile = $request->user()->profile;
                $profile->vehicle = $request->vehicle;
                $profile->save();
                return response()->json(['status'=>true, 'data' => 'Vehicle number Updated'], 200);
            }
            return response()->json(['status'=>false, 'error' => "Update Type Error" ], 401);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "Internal Server Erro sdfsdfsd" ], 405);
        }
    }
}
