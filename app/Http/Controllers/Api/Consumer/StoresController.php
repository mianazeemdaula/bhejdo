<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Grimzy\LaravelMysqlSpatial\Types\Point;

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function near(Request $request, $lat, $lon)
    {
        try{
            $point = new Point($lat, $lon);
            $stores = \App\Store::distance('location',$point, 10)->orderByDistance('location', $point,'asc')->get();
            $stores = \App\Http\Resources\V2\Consumer\NearStoreResource::collection($stores);
            return response()->json(['status'=>true, 'stores' => $stores], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
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
            $stores = \App\LifterLocation::where('location', 'near', [
                '$geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        floatval($request->lat), // longitude
                        floatval($request->lon), // latitude
                    ],
                ],
                '$maxDistance' => intval(4 * 1000),
            ])
            ->where("onwork","1")->get();
            $stores = \App\Http\Resources\V2\Consumer\StoreResource::collection($stores);
            return ['status' => true, 'data' => ['stores' => $stores]];
        }catch(Exception $ex){
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
