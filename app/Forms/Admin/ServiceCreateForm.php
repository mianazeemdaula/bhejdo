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
        ])->add('urdu_name', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Urdu Name'
        ])->add('s_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Service price'
        ])->add('s_status', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Service status' 
        ])->add('min_qty_charges', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Delivery Fee'
        ])->add('min_qty', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Minimum Qty'
        ])->add('max_qty', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Maximum Qty'
        ])->add('cross_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Cross Price'
        ])->add('lifter_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Partner Price'
        ])->add('scale', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Scale'
        ])->add('description', 'textarea',  [
            'label' => 'Description'
        ])->add('image','file',[
            'attr' =>[
                'accept'=>"image/jpeg, image/png, image/jpg",
            ],
            'label' => 'Upload Icon'
        ]);
    }
}
