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

    public function update(Request $request, $id)
    {
        try{
            $offer = Offer::where('promo_code', $id)->where('status',1)->first();
            if($offer == null){
                $data = [
                    'msg' => 'Offer is expire.'
                ];
                return response()->json(['status'=>false, 'data' => $data], 200);
            }
            else if($request->amount < $offer->shopping_limit){
                $data = [
                    'msg' => "Please shop for RS{$offer->shopping_limit} for avaling this offer."
                ];
                return response()->json(['status'=>false, 'data' => $data], 200);
            }else if($offer->credit == 1){
                $data = [
                    'msg' => "You can save upto {$offer->amount}{$offer->type}.",
                ];
                return response()->json(['status'=>true, 'data' => $data], 200);
            }else if($offer->credit == 0){
                $amount = 0;
                if($offer->type == '%'){
                    $amount = round($request->amount * $offer->amount / 100);
                }else if($offer->type == 's'){
                    $amount = $request->amount - $offer->amount;
                }
                $data = [
                    'msg' => "You can save upto {$offer->amount}{$offer->type}.",
                    'amount' => $amount
                ];
                return response()->json(['status'=>true, 'data' => $data], 200);
            }
            $data = ['msg' => 'Some thing not process'];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Expection $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
