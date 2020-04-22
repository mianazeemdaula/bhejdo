<?php

namespace App\Forms\Store;

use Kris\LaravelFormBuilder\Form;

class ProductForm extends Form
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
            'label' => 'Contract Price' 
        ])->add('markeet_price', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Markeet Price'
        ])->add('weight', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Weight (in grams)'
        ])->add('unit', 'text',  [
            'rules' => 'required|max:3',
            'label' => 'Unit'
        ])->add('image','file',[
            'attr' =>[
                'accept'=>"image/jpeg, image/png, image/jpg",
            ],
            'label' => 'Image'
        ]);
    }
}
