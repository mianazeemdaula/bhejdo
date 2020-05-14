<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Offer;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        try{
            $offers = Offer::where('status',1)->latest('id')->get();
            $offers = \App\Http\Resources\V2\Consumer\OfferResource::collection($offers);
            return response()->json(['status'=>true, 'data' => $offers], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }

    }

    public function update(Request $request, $coupon)
    {
        try{
            $response = \App\Helpers\OfferProcess::processOffer($request->user()->id, $coupon, $request->amount);
            return response()->json( $response, 200);
            
        }catch(Expection $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
