<?php

namespace App\Http\Requests\Api\v1;

use App\Models\DiscordUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteRemainderRequest extends FormRequest
{
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

        /**
         * @var string snowflake The snowflake of the DiscordUser
         * NOTE: the failsafe is for Scribe.
         * @see: https://scribe.knuckles.wtf/laravel/troubleshooting#some-weird-behaviour-when-using-formrequests
         * @see 'git show eb10344ce1'
         */
        $snowflake = request('discord_user')?->snowflake ?? 0;

        return [
            'snowflake' => [
                'required',
                'string',
                'digits_between:18,19',
                Rule::in($snowflake),
                'exclude',
            ],
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
