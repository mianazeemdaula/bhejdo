<?php

namespace App\Forms\Admin\Offer;

use Kris\LaravelFormBuilder\Form;

class createOfferForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('title', 'text',  [
            'rules' => 'required|min:5',
            'label' => 'Title'
        ])->add('description', 'text',  [
            'rules' => 'required|min:5',
            'label' => 'Description'
        ])->add('category', 'select',  [
            'choices' => ['bonus' => 'Bonus', 'wallet' => 'Wallet'],
            'label' => 'Category'
        ])->add('type', 'select',  [
            'choices' => ['%' => 'Percentage', 's' => 'Solid'],
            'label' => 'Type'
        ])->add('promo_code', 'text',  [
            'rules' => 'required|min:5|max:5',
            'label' => 'Promo Code'
        ])->add('amount', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Offer Amount'
        ])->add('expiry_date', 'date',  [
            'rules' => 'required',
            'label' => 'Expiry Date'
        ])->add('status', 'choice', [
            'choices' => ['1' => 'Active', '0' => 'Deactive'],
            'expanded' => true,
            'multiple' => false
        ]);
    }
}
