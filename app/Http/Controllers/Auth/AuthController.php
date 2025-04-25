<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\SMS\FarazSmsService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private $farazSmsService;
    private $isTestMode;

    public function __construct(FarazSmsService $farazSmsService)
    {
        $this->farazSmsService = $farazSmsService;
        $this->isTestMode = config('app.env') === 'testing' || config('app.debug');
    }

    public function sendOtp(PhoneNumberRequest $request): JsonResponse
    {
        $phoneNumber = $request->phone_number;

        //search for a user with this phone_number
        $user = '';

        // if (!$user) {
        //     return response()->json(['error' => 'این شماره موبایل در سیستم وحود ندارد'], 422);
        // }

        $otp = $this->makeOtp($phoneNumber);

        if ($this->isTestMode) {
            // In test mode, just return the OTP without actually sending SMS
            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully in the test mode',
                'code' => $otp,
            ], 200);
        }

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

    public function register(UserRegisterRequest $request): User
    {
        $user = User::create([
            'firstName' => $request->first_name,
            'lastName' => $request->last_name,
            'phoneNumber' => $request->phone_number,
        ]);
        
        $user->assignRole($request->role);

        return $user;
    }
}
