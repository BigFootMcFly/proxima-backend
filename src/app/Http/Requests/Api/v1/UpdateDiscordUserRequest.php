<?php

namespace App\Http\Requests\Api\v1;

use App\Traits\FilamentFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDiscordUserRequest extends FormRequest
{

    use FilamentFormRequest;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //maybe add guard (route is already guarded...)
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'snowflake' => [
                'required',
                'string',
                'snowflake',
                'exclude',
            ],
            'user_name'  => 'string|nullable',
            'global_name' => 'string|nullable',
            'avatar' => 'string|nullable',
            'locale' => [
                Rule::in(LOCALES),
                'nullable',
            ],
            'timezone' => 'string|nullable|timezone:all',
        ];
    }
}
