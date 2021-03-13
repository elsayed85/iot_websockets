<?php

namespace App\Http\Controllers\Iot\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
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

    public function logout()
    {
        auth()->user("device")->tokens()->where('id', auth()->user("device")->currentAccessToken()->id)->delete();
        return response()->noContent();
    }
}
