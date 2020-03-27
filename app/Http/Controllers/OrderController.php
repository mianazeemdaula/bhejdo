<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\OrderTransferForm;

class OrderController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = Order::latest('id')->limit(200)->get();
        return view('pages.admin.order.index', compact('collection'));
    }

    public function getTransfer(Order $order)
    {
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
                    floatval($order->longitude), // longitude
                    floatval($order->latitude), // latitude
                ],
            ],
            '$maxDistance' => intval(3 * 1000),
        ])
        ->where('services','all',[intval($order->service_id)])->lists('name','lifter_id');
        $form->addAfter('consumer_id', 'lifter', 'select', [
            'choices' => $lifters
        ]);
        return view('pages.admin.order.transfer', compact('form'));
    }
}
