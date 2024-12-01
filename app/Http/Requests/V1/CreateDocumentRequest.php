<?php

namespace App\Http\Requests\V1;

use App\Http\Enum\V1\ContentTypeEnum;
use App\Models\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class CreateDocumentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:128', Rule::unique('documents', 'title')
                ->where('app_id', request()->app->id)],
            'description' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content' => ['sometimes', 'array'],
            'content.*.type' => ['required', 'string', new Enum(ContentTypeEnum::class)],
            'content.*.data' => ['required', 'array'],
        ];
    }

    public function after(): array
    {
        /** @var App $app */
        $app = request()->app;

        return [
            function (Validator $validator) use ($app) {
                if ($app->hasExceededDocumentLimit()) {
                    $validator->errors()->add(
                        'general',
                        'You have hit your document limit, you can increase your limits by upgrading your plan'
                    );
                }
            }
        ];
    }
}
