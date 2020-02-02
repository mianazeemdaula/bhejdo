<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Order;
use Carbon\Carbon;

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
        // $orders = Order::where('status', 'pending')->get();
        // foreach($orders as $orders){

        // }
    }
}
