<?php
namespace App\Http\Controllers\Auth;

use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneNumberRequest;
use App\Http\Requests\Auth\UserEditRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Core\RealEstateEditRequest;
use App\Models\Otp;
use App\Models\RealEstate;
use App\Models\User;
use App\SMS\FarazSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'image'       => $request->hasFile('image') ? $request->file('image')->store('users', 'public') : null,

        ]);

        $user->assignRole($request->role);

        return $user;
    }

    public function updateUserProfile(UserEditRequest $request)
    {
        $user = auth()->user();

        try {

            $data = $request->only(['first_name', 'last_name']);

            $data['firstName'] = $data['first_name'];
            $data['lastName']  = $data['last_name'];

            if ($request->hasFile('image')) {

                $data['image'] = $this->updateModelImage($user, $request->file('image'), 'users/images');
            }

            $user->fill($data);

            $user->save();

            return response()->json(
                [
                    'message' => ResponseMessages::USER_PROFILE_UPDATED_SUCCESS,
                    'user'    => $user->fresh(),
                ], 200);

        } catch (\Exception $e) {
            Log::error('error in updating the user profile : ', [$e->getMessage()]);
            return response()->json(['message' => ResponseMessages::PROFILE_UPDATED_ERROR], 500);
        }

    }

    public function updateRealEstateProfile(RealEstateEditRequest $request, RealEstate $realestate)
    {

        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->updateModelImage($realestate, $request->file('image'), 'realestates/images');
            }

            $realestate->fill($data);

            $realestate->save();

            return response()->json(['message' => ResponseMessages::REALESTATE_PROFILE_UPDATED_SUCCESS,
                'realestate'                       => $realestate->fresh()], 200);

        } catch (\Exception $e) {
            Log::error('error in updating realestate profile : ', [$e->getMessage()]);
            return response()->json(['message' => ResponseMessages::PROFILE_UPDATED_ERROR], 500);
        }

    }

    private function updateModelImage($model, $newImage, $path)
    {

        $newImagePath = $newImage->store($path);

        if ($model->image && Storage::exists($model->image)) {
            Storage::delete($model->image);
        }

        return $newImagePath;

    }
}
