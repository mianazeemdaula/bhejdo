<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\NotificationForm;

class NotificationController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        $form = $this->form(NotificationForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route("notification.store"),
        ]);
        return view('pages.admin.notifications.create', compact('form'));
    }

    public function store(Request $request)
    {
        return $request->all();
    }
}
