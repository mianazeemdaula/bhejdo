<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Category;
use Auth;
// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Category\UpdateCategoryForm;
use App\Forms\Admin\Category\CreateCategoryForm;

use DB;

class CategoryController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $products = Category::all();
        return view('pages.admin.category.index', compact('products'));
    }

    public function edit($id)
    {
        $product = Category::findOrFail($id);
        $form = $this->form(UpdateCategoryForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('admin.category.update', $id),
            'model' => $product
        ]);
        return view('pages.admin.category.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $form = $this->form(UpdateProductForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->save();
            DB::commit();
            return redirect()->back()->with('status', 'Category updated successfully!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
        }
    }

    public function create()
    {
        $form = $this->form(CreateCategoryForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('admin.category.store'),
        ]);
        return view('pages.admin.category.edit', compact('form'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $form = $this->form(CreateCategoryForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $category = new Category();
            $category->name = $request->name;
            $category->save();
            DB::commit();
            return redirect()->back()->with('status', 'Category added successfully!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
        }
    }
}
