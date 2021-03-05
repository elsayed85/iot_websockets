<?php

namespace App\Http\Controllers\IOT\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\Patient\LoginResource;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $device = Device::where('public_key', $request->public_key)->first();

        if (!$device || !Hash::check($request->private_key, $device->private_key)) {
            throw ValidationException::withMessages([
                'keys' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json((["token" => $device->createToken($device->name)->plainTextToken, 'device_id' => $device->id]));
    }
}
