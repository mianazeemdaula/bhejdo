<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\ServiceCharge;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
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
        return view('pages.super-admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if($user->hasRole('store')){
            ServiceCharge::add($user->id, "Signup Bonus", "bonus", 5000);
        }else if($user->hasRole('lifter')){
            ServiceCharge::add($user->id, "Signup Bonus", "bonus", 1000);
        }
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
        //
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
