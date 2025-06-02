<?php
namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;

class UserCreateAction
{
    public function execute(Request $userData, $role): User
    {
        $user = User::create([
            'firstName'   => $userData->first_name,
            'lastName'    => $userData->last_name,
            'phoneNumber' => $userData->phone_number,
            'image'       => $userData->hasFile('image') ? $userData->file('image')->store('users', 'public') : null,
        ]);

        $user->assignRole($role);

        return $user;
    }

}
