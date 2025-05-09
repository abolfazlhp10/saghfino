<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'area' => 'required|integer|min:1',
            'floor' => 'required|integer|min:0',
            'number_of_floors' => 'required|integer|min:1',
            'rooms' => 'required|integer|min:1',
            'city' => 'required|string|max:100',
            'type_of_wc' => 'required|in:farangi,wc,both',
            'elevator' => 'required|boolean',
            'parking' => 'required|boolean',
            'transaction_type' => 'required|in:sell,rent,mortgage',
            'property_type' => 'required|in:residential,commercial',
            'sell_price' => 'nullable|integer|min:0',
            'rent_price' => 'nullable|integer|min:0',
            'mortgage_price' => 'nullable|integer|min:0',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:4048', // 4MB max per image
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'شناسه کاربر الزامی است.',
            'user_id.exists' => 'کاربر انتخاب شده وجود ندارد.',
            'title.required' => 'عنوان آگهی الزامی است.',
            'description.required' => 'توضیحات آگهی الزامی است.',
            'address.required' => 'آدرس الزامی است.',
            'area.required' => 'متراژ الزامی است.',
            'area.integer' => 'متراژ باید عدد باشد.',
            'area.min' => 'متراژ باید حداقل 1 باشد.',
            'floor.required' => 'طبقه الزامی است.',
            'floor.integer' => 'طبقه باید عدد باشد.',
            'floor.min' => 'طبقه نمی‌تواند منفی باشد.',
            'number_of_floors.required' => 'تعداد طبقات ساختمان الزامی است.',
            'number_of_floors.integer' => 'تعداد طبقات باید عدد باشد.',
            'number_of_floors.min' => 'تعداد طبقات باید حداقل 1 باشد.',
            'rooms.required' => 'تعداد اتاق‌ها الزامی است.',
            'rooms.integer' => 'تعداد اتاق‌ها باید عدد باشد.',
            'rooms.min' => 'تعداد اتاق‌ها باید حداقل 1 باشد.',
            'city.required' => 'شهر الزامی است.',
            'type_of_wc.required' => 'نوع سرویس بهداشتی الزامی است.',
            'type_of_wc.in' => 'نوع سرویس بهداشتی انتخاب شده معتبر نیست.',
            'elevator.required' => 'لطفا مشخص کنید که آسانسور دارد یا خیر.',
            'elevator.boolean' => 'مقدار آسانسور باید بله یا خیر باشد.',
            'parking.required' => 'لطفا مشخص کنید که پارکینگ دارد یا خیر.',
            'parking.boolean' => 'مقدار پارکینگ باید بله یا خیر باشد.',
            'transaction_type.required' => 'نوع معامله الزامی است.',
            'transaction_type.in' => 'نوع معامله انتخاب شده معتبر نیست.',
            'property_type.required' => 'نوع ملک الزامی است.',
            'property_type.in' => 'نوع ملک انتخاب شده معتبر نیست.',
            'sell_price.integer' => 'قیمت فروش باید عدد باشد.',
            'sell_price.min' => 'قیمت فروش نمی‌تواند منفی باشد.',
            'rent_price.integer' => 'قیمت اجاره باید عدد باشد.',
            'rent_price.min' => 'قیمت اجاره نمی‌تواند منفی باشد.',
            'mortgage_price.integer' => 'قیمت رهن باید عدد باشد.',
            'mortgage_price.min' => 'قیمت رهن نمی‌تواند منفی باشد.',
            'images.required' => 'حداقل یک تصویر الزامی است.',
            'images.array' => 'تصاویر باید به صورت آرایه ارسال شوند.',
            'images.*.image' => 'هر فایل باید یک تصویر باشد.',
            'images.*.mimes' => 'فقط تصاویر با فرمت JPEG، PNG و JPG مجاز هستند.',
            'images.*.max' => 'حجم هر تصویر نباید بیشتر از 4 مگابایت باشد.',
        ];
    }
}
