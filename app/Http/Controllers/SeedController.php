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
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Redis;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\PartnerLocation;

use Twilio\Rest\Client;

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
        $consumer = Role::firstOrCreate(['name' => 'company']);

        $city = new \App\City();
        $city->name = 'Lahore';
        $city->save();

        $city = new \App\City();
        $city->name = 'Sargodha';
        $city->save();

        $user = new User();
        $user->name = 'Azeem Rehan';
        $user->mobile = '03017313993';
        $user->email = 'mazeemrehan@gmail.com';
        $user->password = bcrypt('8m8a2r4w');
        $user->address = 'Rasheed u din colony, Depalpur';
        $user->account_type = 'super-admin';
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

        $user = new User();
        $user->name = 'OhYes';
        $user->mobile = '03002233456';
        $user->email = 'ohyes.ltd@gmail.com';
        $user->password = bcrypt('123456');
        $user->address = 'Sabza Zar Colony, Lahore';
        $user->account_type = 'company';
        $user->save();
        $user->assignRole('company');

        // $service = new Service();
        // $service->s_name = 'Dhodo';
        // $service->min_qty = 5;
        // $service->max_qty = 25;
        // $service->min_qty_charges = 50;
        // $service->s_charges = 10;
        // $service->s_price = 90;
        // $service->icon = 'milk_service';
        // $service->s_status = 'active';
        // $service->save();

        // $service = new Service();
        // $service->s_name = 'Dhodo +';
        // $service->min_qty = 5;
        // $service->max_qty = 25;
        // $service->s_price = 110;
        // $service->min_qty_charges = 50;
        // $service->s_charges = 10;
        // $service->icon = 'milk_service';
        // $service->s_status = 'active';
        // $service->save();

        // $service = new Service();
        // $service->s_name = 'Dhodo ++';
        // $service->min_qty = 5;
        // $service->max_qty = 25;
        // $service->s_price = 130;
        // $service->min_qty_charges = 50;
        // $service->s_charges = 10;
        // $service->icon = 'milk_service';
        // $service->s_status = 'active';
        // $service->save();

        // $service = new Service();
        // $service->s_name = 'Cow Milk';
        // $service->min_qty = 5;
        // $service->max_qty = 25;
        // $service->s_price = 100;
        // $service->min_qty_charges = 50;
        // $service->s_charges = 10;
        // $service->icon = 'milk_service';
        // $service->s_status = 'active';
        // $service->save();

        // $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 1,'l_name' => 'Dual Time', 'order_qty' => 100]);

        // $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 2,'l_name' => 'Dual Time', 'order_qty' => 100]);

        // $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 3,'l_name' => 'Dual Time', 'order_qty' => 100]);

        // $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 4,'l_name' => 'Dual Time', 'order_qty' => 100]);

        // $user = new User();
        // $user->name = 'OhYes Store';
        // $user->mobile = '03001234567';
        // $user->email = 'admin@ohyes.ltd';
        // $user->password = bcrypt('8m8a2r4w');
        // $user->address = 'Rasheed u din colony, Depalpur';
        // $user->account_type = 'lifter';
        // $user->reffer_id = "OHYESSTORE";
        // $user->status = "active";
        // $user->save();
        // $user->assignRole('lifter');
        // //$user->services()->sync([1,['level_id' => 1]],[2, ['level_id' => 5]], [3, ['level_id' => 9]]);
        // $user->services()->attach([1 => ['level_id' => 1, 'status'=> 1]]);
        // $user->services()->attach([2 => ['level_id' => 5, 'status'=> 1]]);
        // $user->services()->attach([3 => ['level_id' => 9, 'status'=> 1]]);
        // $user->services()->attach([4 => ['level_id' => 13, 'status'=> 1]]);

        // $user = new User();
        // $user->name = 'Mian AR Rehman';
        // $user->mobile = '03014262629';
        // $user->email = 'mianarrehman@gmail.com';
        // $user->password = bcrypt('8m8a2r4w');
        // $user->address = 'Rasheed u din colony, Depalpur';
        // $user->account_type = 'consumer';
        // $user->status = "active";
        // $user->save();

        // $user->assignRole('consumer');

        return "Data Added Successfully";
    }

    public function pusher()
    {
        return event(new NewLocation(1,25.322,32.656556));
    }

    public function test()
    {
        $user = \App\User::find(2);
        return $user->city;
        // $level = Level::firstOrCreate(['service_id' => 5,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 5,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 5,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 5,'l_name' => 'Dual Time', 'order_qty' => 100]);

        // $level = Level::firstOrCreate(['service_id' => 6,'l_name' => 'Part Time', 'order_qty' => 25]);
        // $level = Level::firstOrCreate(['service_id' => 6,'l_name' => 'Full Time', 'order_qty' => 50]);
        // $level = Level::firstOrCreate(['service_id' => 6,'l_name' => 'Over Time', 'order_qty' => 75]);
        // $level = Level::firstOrCreate(['service_id' => 6,'l_name' => 'Dual Time', 'order_qty' => 100]);
        // $ue = "";
        // $balance = \App\Bonus::deduct(2, "Test Fee", "reffer", 20);

        // //return $balance;
        // return $user = User::find(2)->services->pluk('id');
        
        // $user = User::find(2);
        // $user->removeRole('lifter');
        // $user->assignRole('store');
        // return $user;
        // $order = \App\Order::find(2);
        // return  new \App\Http\Resources\Order\Order($order);
        // return \App\Order::leftJoin('reviews','reviews.order_id','=','orders.id')->where('orders.lifter_id',2)->where('reviews.type','lifter')->avg('reviews.starts');
        // if (Cache::has('user')) {
        //     return "Has Data";
        // }
        // $value = Cache::remember('user', 60*60*60, function () {
        //     return User::find(2);
        // });

        // return $value;
        // // $user->services->save();
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
       // return $user->services()->wherePivot('status',1)->pluck('id')->toArray();
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

    public function seedLocations()
    {
        $string = '{"6":{"lat":30.6745433,"lon":73.6661042,"name":"Azeem Dhodhi","token":"c2bEx4WRQ1Kt_TLw9V8kQr:APA91bHWvSB218knBLSboldLuGeTISaI4PhDcynBtr6g4V3FUogYZGiYG7gt9JHNGwW1n2WTryZX0jjK27Tw_0qj_KPuKLXKp67dj6HvkffNJFa8y_SgkL02A0qH5O0xImk5wZGDYF4X","onwork":1,"services":[1,2,3]},"8":{"lat":30.6637588,"lon":73.65635939999999,"name":"Zeeto Doodhi","token":"fj4oykhlt2M:APA91bE2b2s6VwBhsqlROtDvY9Gi0SueyjPcF8k1a6RqYowCgVDoxTytiDkjNlOUar2GZ2K4LBDaIOJnFCj-6m-924sXm_fXwlGLZsz_5nCrPtazj-IF-4NEGSX-vW-F3up7VFMwcKv7","onwork":1,"services":[1,2,3]},"17":{"lat":31.512215,"lon":74.2634549,"name":"Imran Ashraf","token":"dMU-mINxdIg:APA91bGxnpuoBEvZbLMg0f_19CbtCQ8U5OyF-F2llBOQlTuTX52nVGCx3r7DiiuBYbdiwW7ClEe6E1iX9op-IXhaRvHPHLT_jXsYd9y6sF-KR1l2WHPG62zIO_l2e1PJPruBu4OQwRgk","onwork":1,"services":[1,2,3]},"19":{"lat":31.5121837,"lon":74.2634267,"name":"Imran Ashraf","token":"eMxM8GamNn0:APA91bHyOSqI7S3QF0cZGkliYjIZ2lDYgX4sVJPTkzLLkS-2epU4weBHyIxqm1siItwwjuNzmUBPSuUdPtWXq6s6axFKsxgmfzppt8X2sgIrsMqVUyVQqTNgc2ZTfzkl8ITftyQm1Srr","onwork":1,"services":[1,2,3]},"20":{"lat":31.5000906,"lon":74.24819489999999,"name":"Muhammad Ramzan","token":"efmZUEft1hM:APA91bEIR9NZPhNWRmS232CbTHge6q8K7gs3bgSbsEHgzrEkG9WK2_haQ2Q_hnH-2fIbPirGeO7HSEulV--Ps9fw_CXaNON9XOxhF6iUVAhr_-VJHmfHtMLWm1-KdQbumZLgY-UptoOb","onwork":1,"services":[1,2,3]},"21":{"lat":31.5202652,"lon":74.26940100000002,"name":"Mutahir","token":"ethVLDcZMG0:APA91bEHLOEz8EfjtcwtWLQho6K1sSsn4XSWDd_oOhxbLWY12ZYUp-KJwAtNxOApedLX2kGUQPxD6zqA3KDgg6CHHyXrG0AruXqEePddlh2qrHua0kcjmRdG3K6cnE_2K0lzEz1kwJ_S","onwork":1,"services":[1,2,3]},"22":{"lat":0,"lon":0,"name":"mian imran","token":"eaz3BDJ-sWQ:APA91bE6WbZUqgaUQThGi3VXdjzQc9qRM6ttA2p2h1TNGzdp_IUVtSO3Jz7Okhd5np26zg52TdSCBqJodWMNf9tm0gfB-cXihbLtcHnOanDN-R82wzhVQc07ugNiWqc7XhgDs5t1MqJh","onwork":1,"services":[1,2,3]},"24":{"lat":31.5184562,"lon":74.28233590000002,"name":"Saqib Ali","token":"fGlWNScysR0:APA91bFOljAXlbZAF46NMkMIdyrwhm4nk-g3gW5cF7_TQm3QwyjBT6cXFXdro--9A8U2bKvyn8zGumCzsoTujWX-SCbXuY1AMzOZk5tFaM2dr2zpiwaRlUnYV0RmkKIOo6ZJoaetbNSv","onwork":1,"services":[1,2,3]},"27":{"lat":0,"lon":0,"name":"Haider Ali","token":"cLGKNHGI5C0:APA91bGvyO_tZzQ25CQBoukoI6iMh9_yIOD1ob3k2v8-xzzSuiHmntXxz2uYN65kY3J-lCvG2arQib4-AHqLlPnpK2b6a3I6erQueSIxP7rKKEricHJVlk9Tk_4Aj09Navh-ELgdLojk","onwork":1,"services":[1,2,3]},"37":{"lat":0,"lon":0,"name":"ali","token":null,"onwork":1,"services":[1,2,3]},"42":{"lat":30.6630571,"lon":73.6599214,"name":"Javed","token":"cGpmbY81GYQ:APA91bEInJcPF70iJOr4HCgknOOBsmp5VX8ESx3Wm-U3KSz6fiDRGdYKIWlbja9cR9RCD2ysvNWY631dLhdEx-5Rjkh5_JD13KzmnHxW0zMMLAuWu3RJH1mCEO1vWPjal3PYFvrGiZT6","onwork":1,"services":[1,2,3]},"43":{"lat":31.499896,"lon":74.2596183,"name":"M MOHSIN TALIB","token":"cChwycz0Urc:APA91bFD9huwxIDoLUCMAcrl7E7nIqKATljno3WYUr2sJh2AItJPwOBcOYtkEsBSVorean0tXMiz7mcyFzpVSli40McvteCbSlkWlATO5mtp5hsD7A2K4FcnmP9riUxVHdodTrQCyFmb","onwork":1,"services":[1,2,3]},"44":{"lat":0,"lon":0,"name":"Muhammad Qadeer","token":null,"onwork":1,"services":[1,2,3]},"45":{"lat":31.507981,"lon":74.2832182,"name":"shahid Khan","token":"f7nz0K-PCm4:APA91bFgj5qFBRgg5hPyZb9lLUrTkQbOA3bMgF8MIqfCECr8zKQVVfrIEzAnWClAVnXl3WmywYmHFmTd2lP2y9F0e20ycflSXOR0yQd-etKAgaoSUhrZjg6qyDrN1yR03SF9OLemo-1h","onwork":1,"services":[1,2,3]},"47":{"lat":31.5055425,"lon":74.2857816,"name":"Junaid","token":"eiUN68IkTCg:APA91bFziSzkCe5gMj9-L3kPEGtqMnne9olATbULNDl52obWZHQ0XwewT44b9kwZiktwDDxCJ3EvtCPrLAn2fd873Gg6N5FAHwlPVXBArNqZbvZdKZnG3txlMzdK3_tFr7Fcl2qz2rBB","onwork":1,"services":[1,2,3]},"48":{"lat":0,"lon":0,"name":"Mubbasher Ahmad","token":null,"onwork":1,"services":[1,2,3]},"69":{"lat":0,"lon":0,"name":"Tajamal Abbas","token":"cDMgoGESSh0:APA91bGPcOQpSGuhar6Rf8LqauymWKsYi5bmo-xbwsMG2NzvCKc3XxXqWHeT0zeWwmQpcb69yyRC0RVPE-3x3R_vbPM9KKOzLfGZhFCRst-Nwyg7jcNi_CiEredUSXmhRvCFPNkwIWuX","onwork":1,"services":[1,2,3]},"72":{"lat":0,"lon":0,"name":"saifullah","token":"fHFiWlFGQSo:APA91bHnpBLUoOfRlivUFmrewE4LhLk-jOZqxaJhwaiUwuqmpdsFI-64yzLcITpjHvQbbDJxMAV47uzYHCl_pMKACnSIM4D3qzTCUy-VCQ5IYOzBCpIC1u2oC4q5S894ANOG3O7nA8GP","onwork":1,"services":[1,2,3]},"80":{"lat":31.4363894,"lon":74.30265989999998,"name":"Muhammad Ismail","token":"d0b9VD9nT-8:APA91bFInkLIAmXvnaZiACFRsP7URCsIcq9w-MhtcjGRK00gi1jAYESKRA1iKs8oEe1ub-mppHQlGP0llpxnKsK_dZ7cBN4d24emNXa_130ySuzj8-_4MbefQQzhBUrXo2pWwVDL1Fcm","onwork":1,"services":[1,2,3]},"85":{"lat":31.5907541,"lon":74.3611372,"name":"imtiaz","token":null,"onwork":1,"services":[1,2,3]},"92":{"lat":31.5060233,"lon":74.2823319,"name":"Bilal","token":null,"onwork":1,"services":[1,2,3]},"99":{"lat":0,"lon":0,"name":"malik","token":null,"onwork":1,"services":[1,2,3]},"123":{"lat":0,"lon":0,"name":"Amir Mustafa","token":"c9NZXVyMsCE:APA91bHgWjoNZ7ac8l5IG0e6hrCnnwMLLBPiiQnINGSSC7aVFnb65yOkltQ7AFXi3tgChhDcDGtq-Yl-ZEEc_Hc8Ay2xDXvB7xWjyK-LIeP48gJMBWZQxSCkJYOdR34sGZ_DMWLuY5cC","onwork":1,"services":[1,2,3]},"138":{"lat":31.5326195,"lon":74.31354729999998,"name":"usman gujjar","token":"fjhHKfO67io:APA91bGqzbFiZXYE0Fa343Ou4WApfjjqES67MBHvwZcm8UnqKJxArJ9rdyETqEcwiHSpz5wvPijSxfbTQGxVwW00FidAbyHYKs1Wv3ckpiiGGS6LwOmKxmX6Pl0jDskEjKZV68gC7GU4","onwork":1,"services":[1,2,3]},"142":{"lat":31.5403981,"lon":74.28507489999998,"name":"Akram","token":"dhZIDVAOuMc:APA91bGBbmg365SVZzfyfX_Gao_w8mv9Hy5vhsNUTWRedYZSf5D7Fi2o7i51JHJhANGdseg59vKcsGsH4gJ1Z4ziuXjs_OT7NXf4pJFELuv2GiJ-MsMoeAE1o-BR30cSYZI-O7mCQYTW","onwork":1,"services":[1,2,3]},"156":{"lat":31.5202272,"lon":74.2875723,"name":"Nadeem Raja","token":"dwtOHGLwGTY:APA91bHSlMUDk3BZw11HwjFhMbFngBdjSe5Ynl_Irpv-7iHo6idK_cd1by7TZwplZo5H4aaSoS_Osv7MNvYozLi9z72XZxSkdlWlFiCgKsyZ5AgKiF0igjLjQZIvu0MmL4VJQOM1V2MI","onwork":1,"services":[1,2,3]},"172":{"lat":0,"lon":0,"name":"Shahzad Younas","token":null,"onwork":1,"services":[1,2,3]}}';
        $users = json_decode($string);
        foreach($users as $key => $value){
            $partner = new PartnerLocation();
            $partner->partner_id = $key;
            $partner->location = new Point($value->lat, $value->lon);
            $partner->name = $value->name;
            $partner->pushToken = $value->token;
            $partner->services = $value->services;
            $partner->save();
        }
        return PartnerLocation::all();
    }

    public function getLocation()
    {
        $lat = "30.673375";
        $lon = "73.656468";
        return PartnerLocation::MBRContains('location', new Point($lat, $lon), 5)->pluck('name','partner_id');
    }

    public function sendSMS()
    {
        $account_sid = "AC7915496d6ecda502bcb800757aa33e32";
        $auth_token = "878b8adaff0b2422cb3f6dac6126af51";
        $twilio_number = "+12102390721";
        $client = new Client($account_sid, $auth_token);
        $message  = "Hello Dear";
        $recipients = '+923004103160';
        return $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }

}
