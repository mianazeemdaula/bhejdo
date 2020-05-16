<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\ServiceCharge;
use Carbon\Carbon;

use App\Helpers\AndroidNotifications;

// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\UserEditForm;
use App\Forms\Admin\User\CreateUserForm;
use App\Forms\Admin\UserProfileEditForm;


class UserController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        $users = User::all();
        $type = 'admin|support|consumer|lifter|store';
        return view('pages.super-admin.user.index', compact('users', 'type'));
    }

    public function consumer()
    {
        $users = User::role('consumer')->get();
        $type = 'consumer';
        return view('pages.super-admin.user.index', compact('users', 'type'));
    }

    public function lifter()
    {
        $users = User::role('lifter')->get();
        $type = 'lifter';
        return view('pages.super-admin.user.index', compact('users', 'type'));
    }

    public function stores()
    {
        $users = User::role('store')->get();
        $type = 'store';
        return view('pages.super-admin.user.index', compact('users', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->form(CreateUserForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('user.store')
        ]);
        return view('pages.admin.user.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->form(CreateUserForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->password = bcrypt($request->password);
        $user->account_type = $request->role;
        $user->city_id = $request->city_id;
        $user->save();
        $user->assignRole($request->role);
        return redirect()->back()->with('status', 'User Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $profileForm = $this->form(UserProfileEditForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('user.update', $user->id),
            'model' => $user->profile
        ]);
        if($user->hasRole('consumer'))
            return view('pages.super-admin.user.profile', compact('user', 'profileForm'));
        else if($user->hasRole('lifter'))
            return view('pages.super-admin.user.show', compact('user', 'profileForm'));
        else if($user->hasRole('store'))
            return view('pages.super-admin.user.showstore', compact('user', 'profileForm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $form = $this->form(ServiceCreateForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('user.update', $id),
            'model' => $user->profile
        ]);
        return view('pages.admin.super-admin.eprofile_edit.blade', compact('form'));
    }

    public function approved($id)
    {
        $user = User::find($id);
        if($user->cnic_verified_at != null){
            return redirect()->back()->with('status', 'Account already approved!');
        }
        $user->profile()->update([
            'cnic_verified_at' => Carbon::now()->toDateTimeString()
        ]);
        $user->status = 'active';
        $user->save();
        // if($user->hasRole('store')){
        //     ServiceCharge::add($user->id, "Signup Bonus", "bonus", 5000);
        // }else if($user->hasRole('lifter')){
        //     ServiceCharge::add($user->id, "Signup Bonus", "bonus", 1000);
        // }
        $data = ['type' => 'profile'];
        AndroidNotifications::toLifter("Congratulations!","Your account has been approved", $user->pushToken, $data);
        return redirect()->back()->with('status', 'Account Approved Successfully!');
    }

    public function notification($id)
    {
        $user = User::find($id);
        $data = ['url' => 'https://play.google.com/store/apps/details?id=ltd.ohyes.partner'];
        $data = AndroidNotifications::toLifter("OhYes Partner!","Notification test", $user->pushToken, $data);
        return $data;
    }

    public function download($type, $format)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $users = null;
        if($type == 'purchase'){
            $purchasers = \App\CartOrder::groupBy('consumer_id')->select('consumer_id')->get()->toArray();
            $users = User::find($purchasers);
        }else{
            $users = User::role($type)->get();
        }
    
        $columns = array('id', 'name', 'mobile', 'email');
    
        $callback = function() use ($users, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach($users as $user) {
                $mobile = substr($user->mobile,1);
                fputcsv($file, array($user->id, $user->name, "+92{$mobile}", $user->email));
            }
            fclose($file);
        };
        return \Response::stream($callback, 200, $headers);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $request->all();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
