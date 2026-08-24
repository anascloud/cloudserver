<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository{

    public function register(array $data){
        $data = [
            'firstName' => $data['firstName'],
            'phoneNumber' => $data['phoneNumber'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ];
        $user = User::create($data);
        return $user;
    }
}
