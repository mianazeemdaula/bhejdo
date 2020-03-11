<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Events\NewLocation;
use App\Service;
use App\Level;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\Redis;

class SeedController extends Controller
{
    public function Seed()
    {
        $consumer = Role::firstOrCreate(['name' => 'super-admin']);
        $consumer = Role::firstOrCreate(['name' => 'admin']);
        $consumer = Role::firstOrCreate(['name' => 'support']);
        $consumer = Role::firstOrCreate(['name' => 'branch']);
        $consumer = Role::firstOrCreate(['name' => 'store']);
        $consumer = Role::firstOrCreate(['name' => 'lifter']);
        $consumer = Role::firstOrCreate(['name' => 'consumer']);

        $user = new User();
        $user->name = 'Azeem Rehan';
        $user->mobile = '03017313993';
        $user->email = 'mazeemrehan@gmail.com';
        $user->password = bcrypt('8m8a2r4w');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'super-admin';
        $user->reffer_id = "AZEEMREHAN";
        $user->save();
        $user->assignRole('super-admin');

        

        // $user = new User();
        // $user->name = 'Abdur Rehman';
        // $user->mobile = '03334103160';
        // $user->password = bcrypt('123456');
        // $user->address = 'Bustand Depalpur, Depalpur';
        // $user->latitude = 30.672453;
        // $user->longitude = 73.657768;
        // $user->account_type = 'consumer';
        // $user->save();
        // $user->assignRole('consumer');

        // $user = new User();
        // $user->name = 'Ustad Manna';
        // $user->mobile = '03004103161';
        // $user->password = bcrypt('123456');
        // $user->address = 'Dhaki Deplapur, Depalpur';
        // $user->latitude = 30.664448;
        // $user->longitude = 73.655799;
        // $user->account_type = 'store';
        // $user->save();
        // $user->assignRole('store');

        // $user = new User();
        // $user->name = 'Phana Jutt';
        // $user->mobile = '03004103162';
        // $user->password = bcrypt('123456');
        // $user->address = 'Tara Sing, Depalpur';
        // $user->latitude = 30.674908;
        // $user->longitude = 73.625454;
        // $user->account_type = 'lifter';
        // $user->save();
        // $user->assignRole('lifter');

        $service = new Service();
        $service->s_name = 'Dhodo';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->min_qty_charges = 50;
        $service->s_charges = 10;
        $service->s_price = 90;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhodo +';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->s_price = 110;
        $service->min_qty_charges = 50;
        $service->s_charges = 10;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhodo ++';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->s_price = 130;
        $service->min_qty_charges = 50;
        $service->s_charges = 10;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Cow Milk';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->s_price = 100;
        $service->min_qty_charges = 50;
        $service->s_charges = 10;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Part Time', 'order_qty' => 25]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Full Time', 'order_qty' => 50]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Over Time', 'order_qty' => 75]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Dual Time', 'order_qty' => 100]);

        $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Part Time', 'order_qty' => 25]);
        $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Full Time', 'order_qty' => 50]);
        $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Over Time', 'order_qty' => 75]);
        $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Dual Time', 'order_qty' => 100]);

        $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Part Time', 'order_qty' => 25]);
        $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Full Time', 'order_qty' => 50]);
        $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Over Time', 'order_qty' => 75]);
        $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Dual Time', 'order_qty' => 100]);

        $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Part Time', 'order_qty' => 25]);
        $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Full Time', 'order_qty' => 50]);
        $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Over Time', 'order_qty' => 75]);
        $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Dual Time', 'order_qty' => 100]);

        $user = new User();
        $user->name = 'OhYes Store';
        $user->mobile = '03001234567';
        $user->email = 'admin@ohyes.ltd';
        $user->password = bcrypt('8m8a2r4w');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'lifter';
        $user->reffer_id = "OHYESSTORE";
        $user->status = "active";
        $user->save();
        $user->assignRole('lifter');
        //$user->services()->sync([1,['level_id' => 1]],[2, ['level_id' => 5]], [3, ['level_id' => 9]]);
        $user->services()->attach([1 => ['level_id' => 1, 'status'=> 1]]);
        $user->services()->attach([2 => ['level_id' => 5, 'status'=> 1]]);
        $user->services()->attach([3 => ['level_id' => 9, 'status'=> 1]]);
        $user->services()->attach([4 => ['level_id' => 13, 'status'=> 1]]);

        $user = new User();
        $user->name = 'Mian AR Rehman';
        $user->mobile = '03014262629';
        $user->email = 'mianarrehman@gmail.com';
        $user->password = bcrypt('8m8a2r4w');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'consumer';
        $user->reffer_id = "ARREHMAN01";
        $user->status = "active";
        $user->save();

        $user->assignRole('consumer');

        return "Data Added Successfully";
    }

    public function pusher()
    {
        return event(new NewLocation(1,25.322,32.656556));
    }

    public function test()
    {
        // $ue = "";
        // $balance = \App\Bonus::deduct(2, "Test Fee", "reffer", 20);

        // //return $balance;
        // return $user = User::find(2)->services->pluk('id');
        
        $user = User::find(2);
        
        // $user->services->save();
        // $updated = [2,3,4];
        // $index = 0;
        // foreach($user->services as $service){
        //     if(in_array($service->id,$updated)){
        //         $user->services()->updateExistingPivot($service->id, ['status' => 1]);
        //         $key = array_search($service->id,$updated);
        //         unset($updated[$key]);
        //     }else{
        //         $user->services()->updateExistingPivot($service->id, ['status' => 0]);   
        //     }
        // }
        // foreach($updated as $u){
        //     $level = Level::where('service_id',$u)->first();
        //     $user->services()->attach($u, ['status' => 1, 'level_id' => $level->id]);
        // }
        // print_r($updated);
      //  return $user->services;
        return $user->services()->wherePivot('status',1)->pluck('id')->toArray();
        //return $user->services()->wherePivot("service_id",2)->get();

        // Profile Resorce
        // $exits = \PRedis::command('EXISTS',['profile:'.$user->id]);
        // if($exits){
        //     return response()->json(['status'=>true, 'data2' => json_encode(\PRedis::get('profile:'.$user->id))], 200);
        // }
        // $profile = new \App\Http\Resources\Profile\LifterProfile($user);
        // \PRedis::set('profile:'.$user->id, $profile);
        // return response()->json(['status'=>true, 'data' => $profile], 200);
        // return $profile;

        //\App\Wallet::add(2,'Topup', 'topup', 5000);
        // \App\Wallet::deduct(2,'Transfer to #asfsfsd', 'transfer', 5000);
        // \App\Wallet::add(2,'Topup', 'topup', 50);
        // \App\Wallet::deduct(2,'Transfer to service charges', 'transfer', 1200);
        // \App\Wallet::add(2,'Topup', 'topup', 250);
    }

}
