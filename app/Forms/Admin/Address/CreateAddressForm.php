<?php

namespace App\Forms\Admin\Address;

use Kris\LaravelFormBuilder\Form;

class CreateAddressForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('title', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Title'
        ])
        ->add('address', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Address'
        ])
        ->add('latitude', 'text',  [
            'rules' => 'required|min:2|',
            'label' => 'Latitude'
        ])
        ->add('longitude', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Longitude'
        ]);
    }
}
