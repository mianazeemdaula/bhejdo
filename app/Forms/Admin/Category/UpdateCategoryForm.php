<?php

namespace App\Forms\Admin\Category;

use Kris\LaravelFormBuilder\Form;

class UpdateCategoryForm extends Form
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
