<?php

namespace App\Forms\Admin\Sms;

use Kris\LaravelFormBuilder\Form;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateSmsForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('message', 'textarea',  [
            'rules' => 'required|min:5',
            'label' => 'Message'
        ])->add('user', 'select',  [
            'choices' => Role::all()->pluck('name', 'name')->toArray(),
            'label' => 'Users'
        ])->add('number', 'text',  [
            'placeholder' => '03001234567-03001234569',
            'label' => 'Numbers (Range)'
        ])->add('api', 'text',  [
            'choices' => ['oyapi' => 'Oy APi', 'smsapi' => 'SMS Api'],
            'selected' => 'oyapi',
            'label' => 'API Type'
        ]);
    }
}
