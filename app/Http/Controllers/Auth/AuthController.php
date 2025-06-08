<?php

namespace App\Http\Controllers\Auth;

use App\Actions\UserCreateAction;
use App\Actions\UserUpdateAction;
use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\Http\Requests\Auth\UserEditRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use App\SMS\FarazSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Ad;

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

            if ($user->hasRole('real_estate_agent')) {
                $active_ads = $user->ads()->count();
            }

            return response()->json([
                'firstName'        => $user->firstName,
                'lastName'         => $user->lastName,
                'phoneNumber'      => $user->phoneNumber,
                'image'            => $user->image,
                'role'             => $user->roles->first()->name,
                'token'            => $token->plainTextToken,
                'active_ads'       => $active_ads ?? null,
                'token_expires_at' => $expiresAt,
            ], 200);
        } else {
            return response()->json(['message' => ResponseMessages::OTP_IS_FALSE], 404);
        }
    }

    public function register(UserRegisterRequest $request, UserCreateAction $userCreateAction): User
    {
        $user = $userCreateAction->execute($request, $request->role);

        return $user;
    }

    public function updateUserProfile(UserEditRequest $request, UserUpdateAction $userUpdateAction): JsonResponse
    {
        try {
            $user = $userUpdateAction->execute($request);

            return response()->json([
                'message' => ResponseMessages::USER_PROFILE_UPDATED_SUCCESS,
                'user'    => $user->fresh(),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error updating user profile:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => ResponseMessages::PROFILE_UPDATED_ERROR,
            ], 500);
        }
    }
}
