<?php
namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class RateRealEstateRequest extends FormRequest
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
            'rate'           => ['required', 'integer', 'min:0', 'max:5'],
            'real_estate_id' => ['required', 'exists:real_estates,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'rate.required'           => 'امتیاز الزامی است.',
            'rate.integer'            => 'امتیاز باید یک عدد صحیح باشد.',
            'rate.min'                => 'امتیاز نمیتواند کمتر از 0 باشد.',
            'rate.max'                => 'امتیاز نمیتواند بیشتر از 5 باشد.',
            'real_estate_id.required' => 'شناسه ملک الزامی است.',
            'real_estate_id.exists'   => 'شناسه ملک انتخاب شده معتبر نیست.',
        ];
    }
}
