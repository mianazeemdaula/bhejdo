<?php

namespace App\Forms\Admin\Cart;

use Kris\LaravelFormBuilder\Form;

class CreateOrderForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('coupon', 'text',  [
            'rules' => 'min:5',
            'label' => 'Coupon'
        ])->add('delivery_time', 'time',  [
            'rules' => 'required|min:2',
            'label' => 'Delivery Time'
        ]);
    }
}
