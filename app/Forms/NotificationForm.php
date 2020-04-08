<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class NotificationForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('title', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Title'
        ])
        ->add('message', 'textarea',  [
            'rules' => 'required|min:10',
            'label' => 'Message'
        ])->add('to', 'select',  [
            'choices' => ['consumer'=>'Consumer', 'lifter' => 'Lifter'],
            'rules' => 'required|min:2',
            'label' => 'To'
        ]);
    }
}
