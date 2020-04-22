<?php

namespace App\Forms\Admin\User;

use Kris\LaravelFormBuilder\Form;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateUserForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Name'
        ])
        ->add('email', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Email'
        ])->add('mobile', 'text',  [
            'rules' => 'required|min:11|max:11',
            'label' => 'Mobile'
        ])->add('password', 'password',  [
            'rules' => 'required|min:5',
            'label' => 'Password'
        ])->add('city_id', 'select',  [
            'choices' => \App\City::all()->pluck('name', 'id')->toArray(),
            'label' => 'City'
        ])->add('role', 'select',  [
            'choices' => Role::all()->pluck('name', 'name')->toArray(),
            'label' => 'Rols'
        ]);
    }
}
