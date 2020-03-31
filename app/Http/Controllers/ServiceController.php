<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Service;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\ServiceCreateForm;

class ServiceController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $services = Service::all();
        return view('pages.admin.services.index', compact('services'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $form = $this->form(ServiceCreateForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('service.update', $id),
            'model' => $service
        ]);
        return view('pages.admin.services.edit', compact('form'));
    }

    public function update(Request $request, Service $service)
    {
        $form = $this->form(ServiceCreateForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $service->s_name = $request->s_name;
        $service->s_price = $request->s_price;
        $service->s_status = $request->s_status;
        $service->min_qty_charges = $request->min_qty_charges;
        if($request->has('image')){
            $cover = $request->file('image');
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('services'), $imageName);
            //\Storage::disk('public')->put($imageName, $cover);
            $service->img_url = $imageName;
        }
        $service->save();
        return redirect()->back()->with('status', 'Service Updated!');
    }

    public function setservice()
    {
        $user = User::find(1);
        $user->services()->sync([1,3]);
        $user->storelifter()->sync([2,3]);
        $service = Service::find(1);
        return $user->storelifter;
    }
}
