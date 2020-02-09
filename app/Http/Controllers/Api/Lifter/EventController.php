<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Events\NewLocation;
use Elasticsearch\ClientBuilder;

class EventController extends Controller
{
    public function lifterLocation(Request $request)
    {
        event(new NewLocation($request->user()));
        return "Pushed";
    }

    public function locationIndex()
    {
        $data = [
            'body' => [
                'testField' => 'this is the test api'
            ],
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
        ];
        $return = \Elasticsearch::index($data);
        return $return;
    }
}
