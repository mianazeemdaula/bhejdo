<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Illuminate
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\Rule;

// Models
use DB;
use App\User;

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

            $user->assignRole($user->account_type);
            $user->services()->sync([1]);
            $response['token'] = $user->createToken($user->account_type)->accessToken;
            $response['user'] = $user;
            DB::commit();
            return response()->json(['status'=>true, 'data' => $response], 200);
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
            
            $_services = [];
            foreach($user->services as $service){
                $ids = $service->orders()->where('status','delivered')->pluck('id')->toArray();
                $rate = App\LifterReview::whereIn('order_id', $ids)->avg('starts');
                $rate = $rate == null ? 0 : $rate;
                $_services[$service->id] = ['orders' => count($ids), 'rate' => $rate];
            }
            $data = [
                'name' => $user->name,
                'avatar' => $user->avatar,
                'account_type' => $user->getRoleNames()[0],
                'services' => $user->services->pluck('id')->toArray(),
                'services_details' => [], 
                'last_update' => Carbon::now()->timestamp,
                'lifter_id' => $request->user()->id
            ];
            $lifter = App\LifterLocation::where('lifter_id',$request->user()->id)->first();
            if($lifter == null){
                $lifter = App\LifterLocation::create($data);
            }else{
                $lifter->update($data);
            }
            $lifter->unset('services_details');
            $lifter->push('services_details', $_services, true);
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
            return response()->json(['status'=>true], 200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }
}
