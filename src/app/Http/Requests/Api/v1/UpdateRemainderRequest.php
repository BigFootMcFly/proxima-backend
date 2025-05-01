<?php

namespace App\Http\Requests\Api\v1;

use App\Enums\RemainderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRemainderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    protected function prepareForValidation(): void
    {
        if (!$this->request->has('due_at')) {
            return;
        }

        // Convert from timestamp to Carbon object because laravel lacks timestamp validation
        $this->merge([
            'due_at' => Carbon::createFromTimestamp($this->request->get('due_at')),
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'due_at' => 'nullable|date|after:now',//'after:tomorrow'
            'message' => 'string',
            'channel_id' => 'digits_between:18,19|nullable',
            'status' => [
                Rule::in(RemainderStatus::values()),
                'nullable',
            ],
            'error' => 'nullable|string',
        ];
    }


    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (request()->remainder->discord_user_id != request()->discord_user->id) {
                    $validator->errors()->add(
                        'remainder',
                        'Remainder does not belong to the discord user!',
                    );
                }
            },
        ];
    }

}
