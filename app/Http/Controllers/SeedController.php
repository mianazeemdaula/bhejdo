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
        $service->s_name = 'Milk';
        $service->min_qty = 5;
        $service->max_qty = 25;
        $service->min_qty_charges = 50;
        $service->s_charges = 10;
        $service->s_price = 100;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhahi';
        $service->min_qty = 2;
        $service->max_qty = 20;
        $service->s_price = 120;
        $service->min_qty_charges = 50;
        $service->s_charges = 8;
        $service->icon = 'dhahi_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Lasi';
        $service->min_qty = 5;
        $service->max_qty = 20;
        $service->s_price = 80;
        $service->min_qty_charges = 50;
        $service->s_charges = 6;
        $service->icon = 'lasi_service';
        $service->s_status = 'active';
        $service->save();

        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Part Time', 'order_qty' => 25]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Full Time', 'order_qty' => 50]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Over Time', 'order_qty' => 75]);
        $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Dual Time', 'order_qty' => 100]);

        $user = new User();
        $user->name = 'Azeem Rehan';
        $user->mobile = '03004103160';
        $user->email = 'abc@gmail.com';
        $user->password = bcrypt('123456');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'lifter';
        $user->reffer_id = "azeemlifte";
        $user->status = "active";
        $user->save();
        $user->assignRole('lifter');
        $user->services()->sync([1]);

        return "Data Added Successfully";
    }

    public function pusher($message)
    {
        event(new NewLocation($message));
        return "Pushed";
    }
}
