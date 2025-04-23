<?php

namespace App\SMS;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class FarazSmsService
{
    private const BASE_URL = 'https://ippanel.com/patterns/pattern';
    private const TIMEOUT = 30;
    private const CONNECT_TIMEOUT = 10;

    private string $username;
    private string $password;
    private string $from;
    private string $patternCode;

    public function __construct()
    {
        $this->username = config('services.farazsms.username');
        $this->password = config('services.farazsms.password');
        $this->from = config('services.farazsms.from');
        $this->patternCode = config('services.farazsms.pattern_code');

        if (empty($this->username) || empty($this->password)) {
            throw new \RuntimeException('Faraz SMS credentials are not configured');
        }
    }

    /**
     * Send SMS using pattern
     *
     * @param array $to Recipient phone numbers
     * @param array $inputData Pattern variables
     * @return array Response from API
     * @throws \Exception
     */
    public function sendPatternSms(int $otp,array $to, array $inputData): JsonResponse
    {
        $this->validateInput($to, $inputData);

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->connectTimeout(self::CONNECT_TIMEOUT)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get(self::BASE_URL . "?username=" . $this->username . "&password=" . urlencode($this->password) . "&from=$this->from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($inputData)) . "&pattern_code=$this->patternCode");


            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                ],200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $response->json()['message'] ?? 'Unknown error',
                
            ],400);

        } catch (RequestException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ],500);
        }
    }

    /**
     * Validate input parameters
     *
     * @param array $to
     * @param array $inputData
     * @throws \InvalidArgumentException
     */
    private function validateInput(array $to, array $inputData): void
    {
        if (empty($to)) {
            throw new \InvalidArgumentException('Recipient phone numbers cannot be empty');
        }

        if (empty($inputData)) {
            throw new \InvalidArgumentException('Input data cannot be empty');
        }

        foreach ($to as $phone) {
            if (!preg_match('/^09\d{9}$/', $phone)) {
                throw new \InvalidArgumentException('Invalid phone number format: ' . $phone);
            }
        }
    }
}
