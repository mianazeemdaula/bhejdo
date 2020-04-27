<?php

namespace App\Forms\Admin\Category;

use Kris\LaravelFormBuilder\Form;

class CreateCategoryForm extends Form
{
    public function buildForm()
    {
        $this
        ->add('name', 'text',  [
            'rules' => 'required|min:2',
            'label' => 'Category Name'
        ]);
    }
}
