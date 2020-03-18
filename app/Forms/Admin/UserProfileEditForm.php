<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class UserProfileEditForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('cnic', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'CNIC'
        ])
        ->add('dob', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Mobile'
        ])->add('vehicle', 'DOB',  [
            'rules' => 'required|min:2',
            'label' => 'vehicle'
        ])->add('cnic_expiry', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'CNIC EXPIRY'
        ])->add('driving_licene_expiry', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'DRIVING LICENCE EXIRY'
        ]);
    }
}
