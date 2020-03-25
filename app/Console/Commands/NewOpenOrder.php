<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Order;
use Carbon\Carbon;
use App\Helpers\OrderProcess;

class NewOpenOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openorder:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open Orders which are not assigned or no lifter avaialable in that area';

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
        $orders = Order::where('created_at', '<', Carbon::now()->subSeconds(60)->toDateTimeString())
        ->where('lifter_id',2)->where('status','created')->get();
        //$orders = Order::where('lifter_id',2)->get();
        foreach($orders as $order){
            $response = OrderProcess::orderCreated($order);
        }
        $this->info('Notifications send to every lifter');
    }
}
