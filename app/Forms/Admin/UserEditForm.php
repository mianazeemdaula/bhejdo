<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class UserEditForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Name'
        ])
        ->add('mobile', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Mobile'
        ])->add('email', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Email'
        ])->add('profile.cnic', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'CNIC'
        ]);
    }
}
