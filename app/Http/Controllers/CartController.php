<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Address;
use App\User;
use App\CartOrder;
use DB;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Cart\CreateOrderForm;

class CartController extends Controller
{
    use FormBuilderTrait;

    public function index($user, $address)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($user, $address)
    {
        $user = User::find($user);
        $address = Address::find($address);
        $form = $this->form(CreateOrderForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('user.address.cart.store',[$user->id, $address->id])
        ]);
        $products = \App\Product::where('city_id',$user->city_id)->get();
        return view('pages.admin.user.address.cart.create', compact('form','user','address','products'));
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
