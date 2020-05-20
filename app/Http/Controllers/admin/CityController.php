<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\City\CreateCityForm;

use App\City;
use DB;
class CityController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $cities = City::all();
        return view('pages.admin.city.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->form(CreateCityForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('admin.city.store'),
        ]);
        return view('pages.admin.city.edit', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $form = $this->form(CreateCityForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $city = new City();
            $city->name = $request->name;
            $city->open_time = $request->open_time;
            $city->close_time = $request->close_time;
            $city->bullet_charges_cross = $request->bullet_charges_cross;
            $city->bullet_charges = $request->bullet_charges;
            $city->delivery_charges_cross = $request->delivery_charges_cross;
            $city->delivery_charges = $request->delivery_charges;
            $city->normal_delivery_time = $request->normal_delivery_time;
            $city->bullet_delivery_time = $request->bullet_delivery_time;
            $city->free_delivery_shopping = $request->free_delivery_shopping;
            $city->status = $request->status;
            $city->save();
            DB::commit();
            return redirect()->back()->with('status', 'City added successfully!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
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
        $city = City::findOrFail($id);
        $form = $this->form(CreateCityForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('admin.city.update', $id),
            'model' => $city
        ]);
        return view('pages.admin.city.edit', compact('form'));
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
        try {
            DB::beginTransaction();

            $form = $this->form(CreateCityForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $city = City::find($id);
            $city->name = $request->name;
            $city->open_time = $request->open_time;
            $city->close_time = $request->close_time;
            $city->bullet_charges_cross = $request->bullet_charges_cross;
            $city->bullet_charges = $request->bullet_charges;
            $city->delivery_charges_cross = $request->delivery_charges_cross;
            $city->delivery_charges = $request->delivery_charges;
            $city->normal_delivery_time = $request->normal_delivery_time;
            $city->bullet_delivery_time = $request->bullet_delivery_time;
            $city->free_delivery_shopping = $request->free_delivery_shopping;
            $city->status = $request->status;
            $city->save();
            DB::commit();
            return redirect()->back()->with('status', 'City updated successfully!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
        }
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
