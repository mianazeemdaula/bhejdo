<?php

namespace App\Forms\Admin\City;

use Kris\LaravelFormBuilder\Form;

class CreateCityForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'City Name'
        ])->add('open_time', 'time',  [
            'rules' => 'required',
            'label' => 'Open Time'
        ])->add('close_time', 'time',  [
            'rules' => 'required',
            'label' => 'Close Time'
        ])->add('status', 'choice', [
            'choices' => ['1' => 'Active', '0' => 'Deactive'],
            'expanded' => true,
            'multiple' => false
        ]);
    }
}
