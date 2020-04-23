<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\CartOrder;

class CartOrderController extends Controller
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
        DB::beginTransaction();
        try{
            // $validator = Validator::make( $request->all(), [
            //     'consumer_id' => 'required',
            //     'qty' => 'required',
            //     'price' => 'required',
            //     'address' => 'required',
            //     'latitude' => 'required',
            //     'longitude' => 'required',
            //     'service_id' => 'required'
            // ]);
    
            // if ($validator->fails()) {
            //     return response()->json(['error'=>$validator->errors()], 401);
            // }
            return response()->json(['status'=>true, 'data' => $request->all()], 200);

            // +-----------------+---------------------+------+-----+---------+----------------+
// | Field           | Type                | Null | Key | Default | Extra          |
// +-----------------+---------------------+------+-----+---------+----------------+
// | id              | bigint(20) unsigned | NO   | PRI | NULL    | auto_increment |
// | consumer_id     | bigint(20) unsigned | NO   |     | NULL    |                |
// | sotre_id        | bigint(20) unsigned | YES  |     | NULL    |                |
// | lifter_id       | bigint(20) unsigned | YES  |     | NULL    |                |
// | payment_id      | bigint(20) unsigned | NO   |     | 1       |                |
// | address_id      | int(11)             | NO   |     | NULL    |                |
// | charges         | int(11)             | NO   |     | 0       |                |
// | delivery_time   | time                | NO   |     | NULL    |                |
// | note            | varchar(255)        | YES  |     | NULL    |                |
// | type            | int(11)             | NO   |     | 1       |                |
// | consumer_bonus  | int(11)             | NO   |     | NULL    |                |
// | store_amount    | int(11)             | NO   |     | NULL    |                |
// | lifter_amount   | int(11)             | NO   |     | NULL    |                |
// | payable_amount  | int(11)             | NO   |     | NULL    |                |
// | status          | varchar(15)         | NO   |     | NULL    |                |
// | created_at      | timestamp           | YES  |     | NULL    |                |
// | updated_at      | timestamp           | YES  |     | NULL    |                |
// | consumer_wallet | int(11)             | YES  |     | 0       |                |
// +-----------------+---------------------+------+-----+---------+----------------+

            $order = new CartOrder();
            $order->consumer_id = $request->user()->id;
            $order->lifter_id = 2;
            $order->service_id = $request->service_id;
            $order->qty = $request->qty;
            $order->price = $service->s_price;
            $order->note = $request->note;
            
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            $order->deliver_time = \App\Helpers\TimeHelper::parseTime($request->preffer_time);
            if($request->has('sample')){
                $order->type = 3;
                $order->charges = 0;
            }
            else if($request->qty < $service->min_qty){
                $order->charges = $service->min_qty_charges;
            }else{
                $order->charges = 0;
            }

            if($request->has('shift')){
                $order->shift = $request->shift;
                
                $order->delivery_time = Carbon::now();
            }else{
                $order->delivery_time = $request->delivery_time;
            }
            $order->save();
            // $response = OrderProcess::orderAssign($order);
            // DB::commit();
            // $data = ['msg' => 'Order has placed successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => $data], 200);
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
