<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ScheduleOrder;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\OrderTransferForm;

class ScheduleOrderController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = ScheduleOrder::latest('id')->get();
        return view('pages.admin.subscription.index', compact('collection'));
    }

    public function getTransfer($id)
    {
        $order = Order::find($id);  
        $form = $this->form(OrderTransferForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => url("order/transfer/{$order->id}"),
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
            '$maxDistance' => intval(10 * 1000),
        ])
        ->where('services','all',[intval($order->service_id)])->pluck('name','lifter_id');
        return $lifters;
        $form->addAfter('consumer_id', 'lifter', 'select', [
            'choices' => $lifters
        ]);
        return view('pages.admin.order.transfer', compact('form'));
    }

    public function show($id)
    {
        $order = Order::find($id);
        return view('pages.admin.order.show', compact('order'));
    }
}
