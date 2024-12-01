<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guest();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'between:6,40'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'between:6,40'],
        ];
    }
}
