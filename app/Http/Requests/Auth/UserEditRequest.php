<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
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
            'user_id'    => ['required', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'image'      => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:4096'],
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required'     => 'شناسه کاربر الزامی است.',
            'user_id.exists'       => 'شناسه کاربر نامعتبر است یا وجود ندارد.',
    
            'first_name.required'  => 'وارد کردن نام الزامی است.',
            'first_name.string'    => 'نام باید فقط شامل حروف باشد.',
            'first_name.max'       => 'نام نباید بیش از ۲۵۵ کاراکتر باشد.',
    
            'last_name.required'   => 'وارد کردن نام خانوادگی الزامی است.',
            'last_name.string'     => 'نام خانوادگی باید فقط شامل حروف باشد.',
            'last_name.max'        => 'نام خانوادگی نباید بیش از ۲۵۵ کاراکتر باشد.',
    
            'image.image'          => 'فایل انتخاب‌شده باید یک تصویر باشد.',
            'image.mimes'          => 'فرمت تصویر باید png، jpg یا jpeg باشد.',
            'image.max'            => 'حجم تصویر نباید بیشتر از ۴ مگابایت باشد.',
        ];
    }
    

}
