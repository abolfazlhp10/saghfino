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
}
