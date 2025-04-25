<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => [
                'required',
                'string',
                'regex:/^09\d{9}$/',
                'size:11',
                'unique:users,phoneNumber'
            ],
            'role' => ['required', 'string', 'in:user,real_estate_agent']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'نام الزامی است.',
            'last_name.required' => 'نام خانوادگی الزامی است.',
            'phone_number.regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.',
            'phone_number.size' => 'شماره موبایل باید دقیقاً 11 رقم باشد.',
            'phone_number.required' => 'شماره موبایل الزامی است.',
            'phone_number.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'role.required' => 'نقش کاربر الزامی است.',
            'role.in' => 'نقش کاربر باید یکی از مقادیر user یا real_estate_agent باشد.',
        ];
    }
}
