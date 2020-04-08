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
        ->add('status', 'select',  [
            'choices' => ['assigned' => 'assigned'],
            'rules' => 'required|min:2',
            'label' => 'Status'
        ]);
    }
}
