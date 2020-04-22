<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Kris\LaravelFormBuilder\FormBuilderTrait;

use App\Forms\Admin\PermissionForm;

class PermissionController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = Permission::all();
        return view('admin.permission.index', compact('collection'));   
    }

    public function create()
    {
        $form = $this->form(PermissionForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('permission.store')
        ]);
        return view('admin.permission.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(PermissionForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $permission = Permission::create(['name' => $request->name]);
        return redirect()->back()->with('status', 'Permission Created!');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $form = $this->form(PermissionForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('permission.update', [$id]),
            'model' => $permission
        ]);
        return view('admin.permission.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = $this->form(PermissionForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        return redirect()->back()->with('status', 'Permission Updated!');
    }
}
