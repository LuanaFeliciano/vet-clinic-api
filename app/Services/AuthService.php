<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{   //registrar clinica e admin
    public function registerTenant(array $data): array
    {

        return DB::transaction(function () use ($data) {

            $clinic = Clinic::create([
                'name' => $data['clinicName'],
                'phone' => $data['phone'],
                'type' => $data['clinicType'],
            ]);

            $user = User::create([
                'clinic_id' => $clinic->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
            ]);

            event(new Registered($user)); // dispara email de verificacao
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'clinic' => $clinic,
                'token' => $token,
            ];
        });
    }
}
