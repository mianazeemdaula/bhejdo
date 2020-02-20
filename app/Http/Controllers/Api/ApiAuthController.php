<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


use App\Helpers\BonusProcess;
use App\Helpers\UserHelper;
use App\User;
use App\Order;
use App\LifterReview;
use App\LifterLocation;
use App\Bonus;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    public function login(Request $request){
        $credentials = ['mobile' => $request->mobile, 'password' => $request->password];
        if(Auth::attempt($credentials))
        {
            $user = Auth::user();
            if($user->hasRole('consumer')){
                $success['user'] = $user; 
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

    public function loginLifter(Request $request){
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

    public function registerConsumer(Request $request) {
        try{
            $validator = Validator::make( $request->all(), [
                'name' => 'required',
                'mobile' => 'required|min:11|max:11|unique:users',
                'email' => 'unique:users',
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
            $user->email = $request->email;
            $user->address = $request->address;
            $user->city = $request->city;
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

    public function registerLifter(Request $request) {
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
            $response['token'] = $user->createToken($user->account_type)->accessToken;
            $response['user'] = $user;
            DB::commit();
            return response()->json(['status'=>true, 'data' => $response], 200);
        }catch(Expection $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function accountStatus(Request $request) {
        try{
            return response()->json(['status'=>true, 'data' => $request->user()], 200);
        }catch(Exception $e){
            return response()->json(['success'=>$e], 405);
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

    public function updateLifterPushToken(Request $request) {
        try{
            $user = User::findOrFail($request->user()->id);
            $user->pushToken = $request->pushToken;
            $user->save();
            $ordersCount = Order::where('lifter_id', $user->id)->where('status','delivered')->count();
            $ranking = LifterReview::whereIn('order_id',Order::where('lifter_id', $user->id)->get('id'))->avg('starts');
            $ranking = $ranking == null ? 0 : $ranking;
            $data = ['user' => $user , 'stars' => $ranking, 'count' => $ordersCount];
            // $indexData = [
            //     'body' => [
            //        'lifter_orders' => $ordersCount,
            //         'star_rating' => $ranking,
            //         'name' => $user->name,
            //         'avatar' => $user->avatar,
            //         'account_type' => $user->getRoleNames()[0],
            //         'level' => $user->level->id,
            //         'services' => $user->services->pluck('id')->toArray(),
            //         'last_update' => $currentMilliSecond = (int) (microtime(true) * 100),
            //         'lifter_id' => $request->user()->id
            //     ],
            //     'index' => 'lifter_location',
            //     'id' => 'lifter_'.$request->user()->id,
            // ];
            // $return = \Elasticsearch::index($indexData);
            $data = [
                'orders' => $ordersCount,
                'rating' => $ranking,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'account_type' => $user->getRoleNames()[0],
                'services' => $user->services->pluck('id')->toArray(),
                'last_update' => Carbon::now()->timestamp,
                'lifter_id' => $request->user()->id
            ];
            $location = LifterLocation::where('lifter_id', $request->user()->id )->first();
            if($location == null){
                LifterLocation::create($data);
            }else{
                $location->update($data);
            }
            return response()->json(['status'=> true], 200);
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
            return response()->json(['status'=>false], 200);
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
