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

        $messages = [];
        $onQueue = $request->api;
        if(strlen($request->number) >= 11){
            if(strpos($request->number, "-") !== false){
                $ranges =  explode("-",$request->number);
                for ($i = (int) $ranges[0]; $i < (int) $ranges[1] ; $i++) {
                    $messages[] = ['mobile' =>  "0".$i, 'message' => $request->message];
                }
            }else{
                $messages[] = ['mobile' =>  $request->number, 'message' => $request->message];
            }
        }else{
            $users = User::role($request->user)->get();
            $message = $request->message;
            foreach($users as $user){
                $msg = str_replace("%name",$user->name,$message);
                $messages[] = ['mobile' =>  $user->mobile, 'message' => $msg];
                
            }
        }

        $seconds = 0;
        foreach ($messages as $message) {
            \App\Jobs\SendSmsJob::dispatch($messag['mobile'], $message['message'])->onQueue($onQueue)->delay(now()->addSeconds($seonds));
            $seconds += 6;
        }
        return redirect()->back()->with('status', 'Job for sms created successfully!');
    }
}
