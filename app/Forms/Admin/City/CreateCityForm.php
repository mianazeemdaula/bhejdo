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
        ])->add('bullet_charges_cross', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Bullet Delivery Charges Cross'
        ])->add('bullet_charges', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Bullet Delivery Charges'
        ])->add('delivery_charges_cross', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Noraml Delivery Charge Cross'
        ])->add('delivery_charges', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Normal Delivery Charges'
        ])->add('normal_delivery_time', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Normal Delivery Time (in minutes)'
        ])->add('bullet_delivery_time', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Bullet Delivery Time (in minutes)'
        ])->add('status', 'choice', [
            'choices' => ['1' => 'Active', '0' => 'Deactive'],
            'expanded' => true,
            'multiple' => false
        ]);
    }
}
