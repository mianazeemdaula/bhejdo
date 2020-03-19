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
use App\Forms\Admin\UserProfileEditForm;


class UserController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        $users = User::all();
        return view('pages.super-admin.user.index', compact('users'));
    }

    public function consumer()
    {
        $users = User::role('consumer')->get();
        return view('pages.super-admin.user.index', compact('users'));
    }

    public function lifter()
    {
        $users = User::role('lifter')->get();
        return view('pages.super-admin.user.index', compact('users'));
    }

    public function store()
    {
        $users = User::role('store')->get();
        return view('pages.super-admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('pages.super-admin.user.show', compact('user', 'profileForm'));
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
