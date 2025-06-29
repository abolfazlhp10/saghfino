<?php

namespace App\Classes;

class ResponseMessages
{
    const CREATED_SUCCESS              = "آگهی با موفقیت ایجاد شد";
    const CREATE_ERROR                 = "خطا در ایجاد آگهی !";
    const DELETED_SUCCESS              = "آگهی و تصاویر مربوطه با موفقیت حذف شدند";
    const DELETE_ERROR                 = "خطا در حذف آگهی !";
    const SAVED_SUCCESS                = "آگهی با موفقیت ذخیره شد";
    const SAVE_ERROR                   = "خطا در ذخیره آگهی !";
    const DELETE_FROM_LIST_SUCCESS     = "آگهی از لیست ذخیره‌ها حذف شد";
    const DELETE_FROM_LIST_ERROR       = "خطا در حذف آگهی از لیست ذخیره‌ها !";
    const SAVED_ADS_NOT_FOUND          = "آگهی ذخیره شده ای برای این کاربر وجود ندارد !";
    const ADS_NOT_FOUND                = "آگهی یافت نشد !";
    const DELETE_ALL_SAVED_ADS_SUCCESS = "تمام آگهی های ذخیره شدن کاربر با موفقیت حذف گردید";
    const DELETE_ALL_SAVED_ADS_ERROR   = "ارور در حذف آگهی های ذخیره شده !";
    const DELETE_AUTHORIZE_ERROR       = "شما مجاز به حذف این آگهی نیستید !";
    const NOT_FOUND                    = "! اطلاعات یافت نشد";
    const PHONE_NOT_FOUND              = "این شماره موبایل در سیستم وجود ندارد";
    const REAL_ESTATE_CREATE_SUCCESS   = " اطلاعات املاک شما با موفقیت اضافه شد ";
    const REAL_ESTATE_CREATE_ERROR     = "خطا در عملیات ایجاد املاک !";
    const REAL_ESTATE_NOT_FOUND        = "املاک یافت نشد !";
    const REAL_ESTATE_RATE_ADDED       = "امتیاز شما با موفقیت ثبت گردید ";
    const REAL_ESTATE_RATE_ERROR       = "خطا در عملیات ثبت امتیاز !";
    const CITY_NOT_FOUND               = 'شهری یافت نشد';
    const CITY_FETCH_ERROR             = "خطا در دریافت اطلاعات شهرها !";
    const USER_PROFILE_UPDATED_SUCCESS = "اطلاعات پروفایل شما با موفقیت آپدیت گردید";
    const PROFILE_UPDATED_ERROR   = "ارور در آپدیت اطلاعات !";
    const REALESTATE_PROFILE_UPDATED_SUCCESS = "اطلاعات پروفایل املاک شما با موفقیت آپدیت گردید";
    const ADS_STATUS_UPDATED = "وضعیت آگهی با موفقیت تغییر یافت ";
    const ADS_STATUS_ERROR = "حطا در تغییر وضعیت آگهی !";
    const OTP_IS_FALSE = "کد وارد شده اشتباه است";
    const ADMIN_NOT_FOUND = "نام کاربری یا رمز عبور اشتباه است";
}
