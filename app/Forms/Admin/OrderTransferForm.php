<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class OrderTransferForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('id', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Order ID'
        ])
        ->add('qty', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service price'
        ])->add('price', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Service status'
        ])->add('consumer_id', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Consumer'
        ]);
    }
}
