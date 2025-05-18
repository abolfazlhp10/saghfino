<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use App\SMS\FarazSmsService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    private $farazSmsService;
    private $isTestMode;

    public function __construct(FarazSmsService $farazSmsService)
    {
        $this->farazSmsService = $farazSmsService;
        $this->isTestMode      = config('app.env') === 'testing' || config('app.debug');
    }

    public function sendOtp(PhoneNumberRequest $request): JsonResponse
    {
        $phoneNumber = $request->phone_number;

        $user = User::where('phoneNumber', $phoneNumber)->first();

        if (! $user) {
            return response()->json(['error' => ResponseMessages::PHONE_NOT_FOUND], 422);
        }

        $otp = $this->makeOtp($phoneNumber);

        if ($this->isTestMode) {
            // In test mode, just return the OTP without actually sending SMS
            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully in the test mode',
                'code'    => $otp,
            ], 200);
        }

        $response = $this->farazSmsService->sendPatternSms(
            $otp,
            [$phoneNumber],
            [
                'verification-code' => $otp,
            ]
        );

        return $response;
    }

    private function makeOtp($phoneNumber)
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'phoneNumber'  => $phoneNumber,
            'code'         => $otp,
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

            $user      = User::where('phoneNumber', $request->phone_number)->first();
            $token     = $user->createToken('user');
            $expiresAt = now()->addMinutes(config('sanctum.expiration'));

            return response()->json([
                'firstName'        => $user->firstName,
                'lastName'         => $user->lastName,
                'phoneNumber'      => $user->phoneNumber,
                'image'            => $user->image,
                'role'             => $user->roles->first()->name,
                'token'            => $token->plainTextToken,
                'token_expires_at' => $expiresAt,
            ], 200);
        } else {
            return response()->json(['message' => 'The code is false'], 404);
        }
    }

    public function register(UserRegisterRequest $request): User
    {
        $user = User::create([
            'firstName'   => $request->first_name,
            'lastName'    => $request->last_name,
            'phoneNumber' => $request->phone_number,
            'image'       => $request->hasFile('image') ? $request->file('image')->store('users', 'public') : null

        ]);

        $user->assignRole($request->role);

        return $user;
    }
}
