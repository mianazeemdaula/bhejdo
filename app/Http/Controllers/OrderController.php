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
        $order = Order::findOrFail($id);
        $order->lifter_id = $request->lifter_id;
        $order->status = $request->status;
        $order->save();
        return redirect()->back()->with('status', 'Order updated!');
    }

    public function show($id)
    {
        $order = Order::find($id);
        return view('pages.admin.order.show', compact('order'));
    }
}
