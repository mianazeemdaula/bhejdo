<?php

namespace App\Forms\Admin\Product;

use Kris\LaravelFormBuilder\Form;

class UpdateProductForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('category_id', 'select',  [
            'choices' => \App\Category::all()->pluck('name', 'id')->toArray(),
            'label' => 'Category'
        ])->add('name', 'text',  [
            'rules' => 'required|min:1',
            'label' => 'Name of product'
        ])->add('urdu_name', 'text',  [
            'label' => 'Urdu Name'
        ])->add('contract_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Contract Price (Company)' 
        ])->add('min_qty_charges', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Delivery Charges on min qty' 
        ])->add('markeet_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Markeet Price (Company)'
        ])->add('sale_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Sale Price'
        ])->add('store_commission', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Store Comission'
        ])
        ->add('lifter_commission', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Lifter Comission'
        ])
        ->add('weight', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Weight (in grams)'
        ])->add('unit', 'text',  [
            'rules' => 'required|max:3',
            'label' => 'Unit'
        ])->add('status', 'choice', [
            'choices' => ['0' => 'Active', '1' => 'Deactive'],
            'expanded' => true,
            'multiple' => false
        ])->add('image','file',[
            'attr' =>[
                'accept'=>"image/jpeg, image/png, image/jpg",
            ],
            'label' => 'Image'
        ]);
    }
}
