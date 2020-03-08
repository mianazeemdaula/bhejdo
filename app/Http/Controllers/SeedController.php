<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Events\NewLocation;
use App\Service;
use App\Level;

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
        $user->reffer_id = "azeem00001";
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
        $service->s_price = 100;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhodo +';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->s_price = 110;
        $service->min_qty_charges = 50;
        $service->s_charges = 11;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhodo ++';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->s_price = 130;
        $service->min_qty_charges = 50;
        $service->s_charges = 13;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Chiken';
        $service->min_qty = 2;
        $service->max_qty = 10;
        $service->s_price = 250;
        $service->min_qty_charges = 50;
        $service->s_charges = 25;
        $service->icon = 'milk_service';
        $service->s_status = 'inactive';
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

        $user = new User();
        $user->name = 'BhejDo Store';
        $user->mobile = '03001234567';
        $user->email = 'admin@bhejdo.com';
        $user->password = bcrypt('123456');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'lifter';
        $user->reffer_id = "azeemlifte";
        $user->status = "active";
        $user->save();
        $user->assignRole('lifter');
        //$user->services()->sync([1,['level_id' => 1]],[2, ['level_id' => 5]], [3, ['level_id' => 9]]);
        $user->services()->attach([3 => ['level_id' => 9]]);
        $user->services()->attach([1 => ['level_id' => 1]]);
        $user->services()->attach([2 => ['level_id' => 5]]);

        $user = new User();
        $user->name = 'Mian AR Rehman';
        $user->mobile = '03014103160';
        $user->email = 'arrehman@gmail.com';
        $user->password = bcrypt('123456');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'consumer';
        $user->reffer_id = "rehan00001";
        $user->status = "active";
        $user->save();

        $user->assignRole('consumer');

        return "Data Added Successfully";
    }

    public function pusher($message)
    {
        event(new NewLocation($message));
        return "Pushed";
    }

    public function test()
    {
        $ue = "";
        $balance = \App\Bonus::deduct(2, "Test Fee", "reffer", 20);

        //return $balance;
        return $user = User::find(2)->services->pluk('id');
        // return $user->services()->find(2)->pivot->level->toJson();
        //return $user->services()->wherePivot("service_id",2)->get();
    }

}
