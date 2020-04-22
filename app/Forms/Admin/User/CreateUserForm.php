<?php

namespace App\Forms\Admin\User;

use Kris\LaravelFormBuilder\Form;

class CreateUserForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'CNIC'
        ])
        ->add('email', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Date of birth'
        ])->add('phone', 'text',  [
            'rules' => 'required|min:11|max:11',
            'label' => 'Mobile'
        ])->add('password', 'password',  [
            'rules' => 'required|min:5',
            'label' => 'Vehicle'
        ])->add('city_id', 'select',  [
            'choices' => \App\City::all()->pluck('name', 'id')->toArray(),
            'label' => 'City'
        ])->add('role', 'select',  [
            'choices' => \App\Role::all()->pluck('name', 'name')->toArray(),
            'label' => 'Rols'
        ]);
    }
}
