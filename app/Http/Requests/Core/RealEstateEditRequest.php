<?php
namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RealEstateEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $realestate = $this->route('realestate');
        return Auth::user()->can('edit', $realestate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'city'    => ['required', 'string', 'max:255'],
            'image'   => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:4096'],
            'address' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'وارد کردن نام الزامی است.',
            'name.string'      => 'نام باید به صورت رشته‌ای از حروف باشد.',
            'name.max'         => 'نام نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'tagline.required' => 'وارد کردن شعار تبلیغاتی الزامی است.',
            'tagline.string'   => 'شعار تبلیغاتی باید به صورت رشته‌ای از حروف باشد.',
            'tagline.max'      => 'شعار تبلیغاتی نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'city.required'    => 'وارد کردن نام شهر الزامی است.',
            'city.string'      => 'شهر باید به صورت رشته‌ای از حروف باشد.',
            'city.max'         => 'نام شهر نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'image.image'      => 'فایل انتخاب‌شده باید یک تصویر باشد.',
            'image.mimes'      => 'فرمت تصویر باید یکی از موارد png، jpg یا jpeg باشد.',
            'image.max'        => 'حجم تصویر نباید بیشتر از ۴ مگابایت باشد.',

            'address.required' => 'وارد کردن آدرس الزامی است.',
            'address.string'   => 'آدرس باید متنی معتبر باشد.',
            'address.max'      => 'آدرس نباید بیشتر از 1000 کاراکتر باشد.',
        ];
    }
}
