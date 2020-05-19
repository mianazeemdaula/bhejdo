<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Address;
use App\User;
use DB;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Address\CreateAddressForm;

class AddressController extends Controller
{
    use FormBuilderTrait;

    public function index($user)
    {
        $user = User::find($user);
        return view('pages.admin.user.address.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($user)
    {
        $form = $this->form(CreateAddressForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('user.address.store',[$user])
        ]);
        return view('pages.admin.user.address.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user)
    {
        try{
            DB::beginTransaction();
            $address = new Address();
            $address->user_id = $user;
            $address->title = strlen(trim($request->title)) == 0 ? "Not Set" : $request->title;
            $address->address = $request->address;
            $address->location = new Point($request->latitude, $request->longitude);
            $address->save();
            DB::commit();
            return redirect()->back()->with('status', 'Address created successfully!');
        }catch(Exception $ex){
            DB::rollBack();
            return redirect()->back()->with('status', 'Something went wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
