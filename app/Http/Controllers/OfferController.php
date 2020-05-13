<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Offer\createOfferForm;
use App\Offer;


class OfferController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        $form = $this->form(createOfferForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route("offer.store"),
        ]);
        return view('pages.admin.notifications.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(createOfferForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        Offer::create($request->all());
        
        return redirect()->back()->with('status', 'Offer created successfully!');
    }

    public function edit($id)
    {
        $offer = Offer::find($id);
        $form = $this->form(ServiceCreateForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('offer.update', $id),
            'model' => $offer
        ]);
        return view('pages.admin.super-admin.eprofile_edit.blade', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = $this->form(createOfferForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        
        return $request->all();
    }
}
