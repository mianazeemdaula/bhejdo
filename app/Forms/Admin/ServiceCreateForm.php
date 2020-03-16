<?php

namespace App\Forms\Admin;

use Kris\LaravelFormBuilder\Form;

class ServiceCreateForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('lyrics', 'textarea')
            ->add('publish', 'checkbox');
    }
}
