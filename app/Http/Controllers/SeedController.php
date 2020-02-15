<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Events\NewLocation;
use App\Service;

class SeedController extends Controller
{
    public function Seed()
    {
        $consumer = Role::firstOrCreate(['name' => 'consumer']);
        $milkLifter = Role::firstOrCreate(['name' => 'lifter']);
        $milkLifterShop = Role::firstOrCreate(['name' => 'store']);

        // $user = new User();
        // $user->name = 'Azeem Rehan';
        // $user->mobile = '03004103160';
        // $user->password = bcrypt('123456');
        // $user->address = 'Rasheed u din colony, Depalpur';
        // $user->latitude = 30.672153;
        // $user->longitude = 73.658889;
        // $user->account_type = 'store';
        // $user->save();
        // $user->assignRole('store');

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
        $service->s_price = 100;
        $service->icon = 'milk_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Dhahi';
        $service->min_qty = 2;
        $service->max_qty = 20;
        $service->s_price = 120;
        $service->icon = 'dhahi_service';
        $service->s_status = 'active';
        $service->save();

        $service = new Service();
        $service->s_name = 'Lasi';
        $service->min_qty = 5;
        $service->max_qty = 20;
        $service->s_price = 80;
        $service->icon = 'lasi_service';
        $service->s_status = 'active';
        $service->save();

        return "Data Added Successfully";
    }

    public function pusher($message)
    {
        event(new NewLocation($message));
        return "Pushed";
    }
}
