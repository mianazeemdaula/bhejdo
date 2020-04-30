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
            'rules' => 'required',
            'label' => 'Users'
        ]);
    }
}
