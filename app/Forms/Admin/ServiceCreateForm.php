<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class ServiceCreateForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('s_name', 'text',  [
            'rules' => 'required|min:2|unique:services',
            'label' => 'Service Name'
        ])
        ->add('s_price', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service price'
        ]);
    }
}
