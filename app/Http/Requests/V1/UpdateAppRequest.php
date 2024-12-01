<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128', Rule::unique('apps', 'name')
                ->where('owner_id', Auth::id())
                ->ignore(request()->app?->id)
            ],
            'description' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
