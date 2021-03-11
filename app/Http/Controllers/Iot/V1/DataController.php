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
        event((new SendPayloadEvent($device->id, $payload)));
        return response()->json([
            'device' => $device->id,
            'success' => true,
            'payload' => $payload
        ]);
    }
}
