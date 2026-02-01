<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;

class ApiEmailVerificationRequest extends FormRequest
{
    public $user;

    public function authorize(): bool
    {
        $this->user = User::find($this->route('id'));

        if (! $this->user) {
            return false;
        }

        if (! hash_equals(sha1($this->user->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        if (! $this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
            event(new Verified($this->user));
        }
    }
}
