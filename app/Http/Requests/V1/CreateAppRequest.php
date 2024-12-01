<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateAppRequest extends FormRequest
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
                ->where('owner_id', Auth::id())],
            'description' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (Auth::user()->hasExceededAppLimits()) {
                    $validator->errors()->add(
                        'general',
                        'You have hit your app limit, contact us to increase your limit'
                    );
                }
            }
        ];
    }
}
