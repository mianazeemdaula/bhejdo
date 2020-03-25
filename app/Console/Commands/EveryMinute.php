<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Order;
use App\OpenOrder;
use Carbon\Carbon;
use App\Helpers\OrderProcess;

class EveryMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'every:mintue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Orders on every minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user  = \App\User::find(6);
        $pushKey = 'AAAAP1dnNFQ:APA91bHezgB1suVurRuRWDoMWbiFZ9oAhIxUtC2kd8Ot0HZ2t0lGAlEpPZHswsV7pnGWuK0eTKfZ6y7ZjIyD5Wt4nP4Fhqm1Q3uvlZVyqaA446ckC2J2QJ94rvA1bFPC_d3AgBkICCIr';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => "Title Goes heere",
            'body' => 'this is body',
            'badge' => 1, 
            'sound' => 'default'
        ];
        $fcmNotification = [
            'to'        => $user->pushToken, //single token
            'notification' => $notification,
            'data' => array_merge([ 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'], [])
        ];
        $headers = [
            'Authorization: key='. $pushKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
