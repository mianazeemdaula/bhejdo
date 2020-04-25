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
        ])->add('description', 'textarea',  [
            'rules' => 'required|min:1',
            'label' => 'Description' 
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
            'label' => 'Store Comission'
        ])
        ->add('lifter_commission', 'number',  [
            'label' => 'Lifter Comission'
        ])->add('oyfee_lifter', 'number',  [
            'label' => 'OY Lifter Fee'
        ])->add('oyfee_store', 'number',  [
            'label' => 'OY Store Fee'
        ])->add('city_leader_commission', 'number',  [
            'label' => 'City Leader Commission'
        ])->add('oy_commission', 'number',  [
            'label' => 'OY Commission'
        ])->add('bonus_deduction', 'number',  [
            'label' => 'Bonus Deduction (Consumer)'
        ])->add('weight', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Weight (in grams)'
        ])->add('unit', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Unit'
        ])->add('status', 'choice', [
            'choices' => ['1' => 'Active', '0' => 'Deactive'],
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
