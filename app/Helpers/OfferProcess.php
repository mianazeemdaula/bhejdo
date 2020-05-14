<?php

namespace App\Helpers;


class OfferProcess {

    static public function processOffer($user, $code, $shopping)
    {
        $offer = \App\Offer::where('promo_code', $code)->where('status',1)->first();
            if($offer == null){
                $data = [
                    'msg' => 'Offer is expire.'
                ];
                return ['status'=>false, 'data' => $data];
            }
            $isAvail = \App\CartOrder::where('consumer_id', $user)->where(function($query) {
                $query->where('status', 'droped')
                      ->orWhere('status', 'confirmed');
            })->where('coupon',$code)->count();
            if($isAvail > 0){
                $data = [
                    'msg' => "You have already avail this offer."
                ];
                return ['status'=>false, 'data' => $data];
            }
            else if($shopping < $offer->shopping_limit){
                $amount = $offer->shopping_limit - $shopping;
                $data = [
                    'msg' => "You have to shop more RS{$amount} to avail this offer."
                ];
                return ['status'=>false, 'data' => $data];
            }else if($offer->credit == 1){
                $data = [
                    'msg' => "Congratulation! You can save upto {$offer->amount}{$offer->type} on {$offer->category}.",
                ];
                return ['status'=>true, 'data' => $data];
            }else if($offer->credit == 0){
                $amount = 0;
                if($offer->type == '%'){
                    $amount = round($shopping * $offer->amount / 100);
                }else if($offer->type == 's'){
                    $amount = $offer->amount;
                }
                $data = [
                    'msg' => "Congratulation! You have to save {$offer->amount}{$offer->type} on {$offer->category}.",
                    'amount' => $amount
                ];
                return ['status'=>true, 'data' => $data];
            }
            $data = ['msg' => 'Some thing not process'];
            return ['status'=>false, 'data' => $data];
    }
        
}