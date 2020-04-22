<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\User;
use App\Service;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\ServiceCreateForm;

class ProductController extends Controller
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
        $service->urdu_name = $request->urdu_name;
        $service->cross_price = $request->cross_price;
        $service->lifter_price = $request->lifter_price;
        $service->description = $request->description;
        $service->scale = $request->scale;
        $service->min_qty_charges = $request->min_qty_charges;
        $service->max_qty = $request->max_qty;
        $service->min_qty = $request->min_qty;
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

    public function create()
    {
        $form = $this->form(ServiceCreateForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('service.store'),
        ]);
        return view('pages.admin.services.edit', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(ServiceCreateForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $service = new Service();
        $service->s_name = $request->s_name;
        $service->s_price = $request->s_price;
        $service->s_status = $request->s_status;
        $service->urdu_name = $request->urdu_name;
        $service->cross_price = $request->cross_price;
        $service->lifter_price = $request->lifter_price;
        $service->description = $request->description;
        $service->scale = $request->scale;
        $service->min_qty_charges = $request->min_qty_charges;
        $service->s_charges = 10;
        $service->max_qty = $request->max_qty;
        $service->min_qty = $request->min_qty;
        if($request->has('image')){
            $cover = $request->file('image');
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('services'), $imageName);
            //\Storage::disk('public')->put($imageName, $cover);
            $service->img_url = $imageName;
        }
        $service->save();
        return redirect()->back()->with('status', 'Service Created!');
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
