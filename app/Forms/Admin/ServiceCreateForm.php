<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class ServiceCreateForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('s_name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service Name'
        ])
        ->add('s_price', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service price'
        ])->add('s_status', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service status'
        ])->add('min_qty_charges', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Delivery Fee'
        ]);
    }
}
