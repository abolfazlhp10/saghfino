<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\SMS\FarazSmsService;

class AuthController extends Controller
{
    private $farazSmsService;

    public function __construct(FarazSmsService $farazSmsService)
    {
        $this->farazSmsService = $farazSmsService;
    }

    public function sendOtp(PhoneNumberRequest $request)
    {
        $phoneNumber = $request->phone_number;

        //search for a user with this phone_number
        $user = '';


        // if (!$user) {
        //     return response()->json(['error' => 'این شماره موبایل در سیستم وحود ندارد'], 422);
        // }

        // using farazsms for sending otp

        $otp = $this->makeOtp();

        $response = $this->farazSmsService->sendPatternSms([$phoneNumber],
            [
                'verification-code' => $otp,
            ]);

        return $response;

    }

    private function makeOtp()
    {
        return $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
