<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Kris\LaravelFormBuilder\FormBuilderTrait;

use App\Forms\Admin\RoleForm;

class RoleController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = Role::all();
        return view('admin.role.index', compact('collection'));   
    }

    public function create()
    {
        $form = $this->form(RoleForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('role.store')
        ]);
        return view('admin.role.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(RoleForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $Role = Role::create(['name' => $request->name]);
        return redirect()->back()->with('status', 'Role Created!');
    }

    public function edit($id)
    {
        $Role = Role::findOrFail($id);
        $form = $this->form(RoleForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('role.update', [$id]),
            'model' => $Role
        ]);
        return view('admin.role.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = $this->form(RoleForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $Role = Role::findOrFail($id);
        $Role->name = $request->name;
        $Role->save();
        return redirect()->back()->with('status', 'Role Updated!');
    }
}
