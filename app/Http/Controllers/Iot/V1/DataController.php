<?php

namespace App\Http\Controllers\Iot\V1;

use App\Events\Device\SendPayloadEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SendPayloadRequest;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class DataController extends Controller
{
    public function send(SendPayloadRequest $request)
    {
        $device = $request->user();
        $payload = json_decode($request->payload);
        if(!$payload){
              $payload = [
                  'bpm_value' => rand(10,90),
                  'temperature_value' => rand(-20 , 25),
                  'light_is_on' => collect([true , false])->random()
              ];
        }
        event((new SendPayloadEvent($device->id, $payload)));
        return response()->json([
            'device' => $device->id,
            'success' => true,
            'payload' => $payload
        ]);
    }
}
