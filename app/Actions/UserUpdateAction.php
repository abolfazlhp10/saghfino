<?php
namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserUpdateAction
{
    public function execute(Request $request): User
    {
        $user = Auth::user();

        $data['firstName'] = $request->first_name;
        $data['lastName']  = $request->last_name;

        if ($request->hasFile('image')) {
            if ($user->image && Storage::exists($user->image)) {
                Storage::delete($user->image);
            }

            $data['image'] = $request->file('image')->store('users/images/');
        }

        $user->fill($data);

        $user->save();

        return $user;
    }

}
