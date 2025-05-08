<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class FilterAdsRequest extends FormRequest
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
            'city' => ['nullable', 'string', 'max:255'],
            'sell_price' => ['nullable', 'string', 'in:asc,desc'],
            'rent_price' => ['nullable', 'string', 'in:asc,desc'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gt:min_price'],
            'pr_type' => ['nullable', 'string', 'in:residential,commercial'],
            'tr_type' => ['nullable', 'string', 'in:sell,rent'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'floor_numbers' => ['nullable', 'integer', 'min:0'],
            'title' => ['nullable', 'string', 'max:255'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'hasElevator' => ['nullable', 'boolean'],
            'hasParking' => ['nullable', 'boolean'],
            'typeOfWc' => ['nullable', 'string', 'in:farangi,wc,both'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city.max' => 'نام شهر نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            'sell_price.in' => 'ترتیب قیمت فروش باید asc یا desc باشد.',
            'rent_price.in' => 'ترتیب قیمت اجاره باید asc یا desc باشد.',
            'min_price.numeric' => 'حداقل قیمت باید عدد باشد.',
            'min_price.min' => 'حداقل قیمت نمی‌تواند منفی باشد.',
            'max_price.numeric' => 'حداکثر قیمت باید عدد باشد.',
            'max_price.min' => 'حداکثر قیمت نمی‌تواند منفی باشد.',
            'max_price.gt' => 'حداکثر قیمت باید بزرگتر از حداقل قیمت باشد.',
            'pr_type.in' => 'نوع ملک باید residential یا commercial باشد.',
            'tr_type.in' => 'نوع معامله باید sell یا rent باشد.',
            'area.numeric' => 'متراژ باید عدد باشد.',
            'area.min' => 'متراژ نمی‌تواند منفی باشد.',
            'floor_numbers.integer' => 'تعداد طبقات باید عدد صحیح باشد.',
            'floor_numbers.min' => 'تعداد طبقات نمی‌تواند منفی باشد.',
            'title.max' => 'عنوان نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            'rooms.integer' => 'تعداد اتاق‌ها باید عدد صحیح باشد.',
            'rooms.min' => 'تعداد اتاق‌ها نمی‌تواند منفی باشد.',
            'hasElevator.boolean' => 'مقدار آسانسور باید 1 یا 0 باشد.',
            'hasParking.boolean' => 'مقدار پارکینگ باید 1 یا 0 باشد.',
            'typeOfWc.in' => 'نوع سرویس بهداشتی باید farangi، wc یا both باشد.',
        ];
    }
} 