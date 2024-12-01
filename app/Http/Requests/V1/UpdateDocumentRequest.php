<?php

namespace App\Http\Requests\V1;

use App\Http\Enum\V1\ContentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateDocumentRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:128', Rule::unique('documents', 'title')
                ->where('app_id', request()->app->id)
                ->ignore(request()->document?->id)
            ],
            'description' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'content' => ['sometimes', 'array'],
            'content.*.type' => ['required', 'string', new Enum(ContentTypeEnum::class)],
            'content.*.data' => ['required', 'array'],
        ];
    }
}
