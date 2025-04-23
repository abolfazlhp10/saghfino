<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Otp;
use App\SMS\FarazSmsService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private $farazSmsService;

    public function __construct(FarazSmsService $farazSmsService)
    {
        $this->farazSmsService = $farazSmsService;
    }

    public function sendOtp(PhoneNumberRequest $request): JsonResponse
    {
        $phoneNumber = $request->phone_number;

        //search for a user with this phone_number
        $user = '';

        // if (!$user) {
        //     return response()->json(['error' => 'این شماره موبایل در سیستم وحود ندارد'], 422);
        // }

        // using farazsms for sending otp

        $otp = $this->makeOtp($phoneNumber);

        $response = $this->farazSmsService->sendPatternSms($otp, [$phoneNumber],
            [
                'verification-code' => $otp,
            ]);
            
        return $response;

    }

    private function makeOtp($phoneNumber)
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'phoneNumber' => $phoneNumber,
            'code' => $otp,
            'created_time' => now(),
        ]);

        return $otp;
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {

        $otp = Otp::where('phoneNumber', $request->phone_number)
            ->where('code', $request->code)
            ->where('created_time', '>', now()->subMinutes(2))
            ->first();

        if ($otp) {
            $otp->delete();
            return response()->json(['message' => 'The code is correct'], 200);
        } else {
            return response()->json(['message' => 'The code is false'], 404);
        }

    }
}
