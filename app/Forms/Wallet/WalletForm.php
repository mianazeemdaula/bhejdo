<?php

namespace App\Forms\Wallet;

use Kris\LaravelFormBuilder\Form;

class WalletForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('description', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Description'
        ])->add('amount', 'number',  [
            'rules' => 'required|min:1',
            'label' => 'Amount'
        ]);
    }
}
