<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;

use App\Address;
use DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try{
            DB::beginTransaction();
            $user = $request->user();
            $address = new Address();
            $address->user_id = $user->id;
            $address->title = strlen(trim($request->title)) == 0 ? "Not Set" : $request->title;
            $address->address = $request->address;
            $address->location = new Point($request->lat, $request->lon);
            $address->save();
            DB::commit();
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            return response()->json(['status'=>true, 'data' => $profile], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
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
        try{
            DB::beginTransaction();
            $user = $request->user();
            $address = Address::find($id);
            $address->title = strlen(trim($request->title)) == 0 ? "Not Set" : $request->title;
            $address->address = $request->address;
            $address->location = new Point($request->lat, $request->lon);
            $address->save();
            DB::commit();
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($user);
            return response()->json(['status'=>true, 'data' => $profile], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
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
