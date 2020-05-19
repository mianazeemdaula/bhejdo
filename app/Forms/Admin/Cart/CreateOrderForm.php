<?php

namespace App\Forms\Admin\Cart;

use Kris\LaravelFormBuilder\Form;

class CreateOrderForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('voucher', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Voucher'
        ]);
    }
}
