<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Sms\CreateSmsForm;
use App\User;
class SmsController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        $form = $this->form(CreateSmsForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route("sms.store"),
        ]);
        return view('pages.admin.notifications.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(CreateSmsForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $users = User::role($request->user)->get();
        return $request->$users->count();
    }
}
