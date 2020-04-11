<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\Bonus;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\OrderTransferForm;
use App\Helpers\AndroidNotifications;

use Carbon\Carbon;
use Validator;
use App\Http\Resources\Order\Order as OrderResource;
use DB;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = Order::latest('id')->limit(200)->get();
        return view('pages.admin.order.index', compact('collection'));
    }

    public function edit($id)
    {
        $order = Order::find($id);  
        $form = $this->form(OrderTransferForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route("order.update", $id),
            'model' => $order
        ]);
        $lifters = \App\LifterLocation::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($order->latitude), // latitude
                    floatval($order->longitude), // longitude
                ],
            ],
            '$maxDistance' => intval(3 * 1000),
        ])
        ->where('services','all',[intval($order->service_id)])->pluck('name','lifter_id');
        $_lifters = [];
        
        foreach($lifters as $key => $value){
            $id = (int) $key;
            $_lifters[$id] = "$value ($key)";
        }

        $form->addAfter('id', 'lifter_id', 'select', [
            'choices' => $_lifters
        ]);
        return view('pages.admin.order.transfer', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = $this->form(OrderTransferForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        DB::beginTransaction();
        try{

            $order = Order::find( $id);
            if($order == null){
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            else if($order->status == 'assigned'){
                return response()->json(['status'=>false, 'data'=>"Order already assigned."], 200);
            }else if($order->status == 'canceled'){
                return response()->json(['status'=>false, 'data'=>"Order canceled by consumer."], 200);
            }
            //
            $order->lifter_id = $request->lifter_id;
            $order->accepted_time = Carbon::now()->toDateTimeString();
            // Bonus Deduction
            $bonus = Bonus::balance($order->consumer_id);
            $bonusDeducted = 0;
            if($bonus != null && $order->type != 3){
                $deductable = $order->qty * 10;  
                if($bonus->balance >= $deductable){
                    $bonusDeducted = $deductable;
                    $order->bonus = $bonusDeducted;
                    Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order", $bonusDeducted);
                }else if($bonus->balance >= 0){
                    $bonusDeducted = $bonus->balance;
                    $order->bonus = $bonusDeducted;
                    Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order", $bonusDeducted);
                }
            }
            $order->status = 'assigned';
            $order->payable_amount = (($order->qty * $order->price) + $order->charges ) - $bonusDeducted;
            $order->save();
            DB::commit();
            // Notifications to consumer
            $orderResource = new OrderResource($order);
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order', 'lifter_id' => $order->lifter_id, 'order' => $orderResource];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            AndroidNotifications::toLifter("Order Assigned", $message, $order->lifter->pushToken, $data);
            return redirect()->back()->with('status', 'Order updated!');
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        return view('pages.admin.order.show', compact('order'));
    }

    public function livePartners($id)
    {
        $order = Order::find($id);
        $lifters = \App\LifterLocation::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($order->latitude), // longitude
                    floatval($order->longitude), // latitude
                ],
            ],
            '$maxDistance' => intval(3 * 1000)
        ])
        ->where("onwork","1")
        ->where('services','all',[$order->service_id])->get();
        //->where('last_update', '>', Carbon::now()->subSeconds(120)->timestamp)

        $data = [];
        foreach($lifters as $lifter){
            $cancle = Cache::has('order_notificaton_'.$lifter->lifter_id."_".$order->id);
            $data[] = ['name' => $lifter->name, 'id' => $lifter->lifter_id, 'notificaton' => $cancle];
        }
        $lifters= \PRedis::command('GEORADIUS',['partner_locations' ,$order->latitude,$order->longitude, 3, 'km', ['WITHDIST','WITHCOORD', 1, 'ASC']]);
        return response()->json(['lifters'=> $data, 'neighbours'=> $lifters], 200);
    }
}
