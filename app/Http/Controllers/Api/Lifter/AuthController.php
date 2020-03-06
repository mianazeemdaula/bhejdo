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
use App\LifterLocation;
use App\Review;

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
            if($user->hasRole('lifter|store')){
                $response['user'] = $user; 
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
                'referred' => 'exists:users,reffer_id|nullable',
                'confirm_password' => 'required|same:password',
                'type' => ['required', Rule::in(['lifter', 'store'])]
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
            $user->account_type = $request->type;
            $user->referred_by = $request->referred;
            $user->status = 'unvarified';
            $user->reffer_id = UserHelper::gerateId($request->name);
            $user->save();
            $profile = $user->profile()->create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude
            ]);

            // if($request->referred != null){
            //     $reffer = User::where('referred_by', $request->referred)->first();
            //     if($reffer != null && $reffer->account_type == 'store'){
            //         $reffer->storLifter()->sync([$user->id]);
            //     }
            // }

            $user->assignRole($user->account_type);
            $user->services()->attach([1 => ['level_id' => 1]]);
            $user->services()->attach([2 => ['level_id' => 5]]);
            $user->services()->attach([3 => ['level_id' => 9]]);
            $response['token'] = $user->createToken($user->account_type)->accessToken;
            $response['user'] = $user;
            DB::commit();
            return response()->json(['status'=>true, 'data' => $response], 200);
        }catch(Expection $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
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
                return response()->json(['status'=> false, 'data' => 'User not found'], 405);
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
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            $lifter = LifterLocation::where('lifter_id',$request->user()->id)->first();
            $data = [
                'name' => $user->name,
                'avatar' => $user->avatar,
                'account_type' => $user->getRoleNames()[0],
                'services' => $user->services->pluck('id')->toArray(),
                'services_details' => [], 
                'last_update' => Carbon::now()->timestamp,
                'lifter_id' => $request->user()->id
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
            return response()->json(['status'=> true, 'data' => $lifter], 200);
        }catch(Exception $e){
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
            return response()->json(['status'=>true, 'data' => ['user' => $request->user()]], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }
}
