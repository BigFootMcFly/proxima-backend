<?php

namespace App\Actions;

use App\Enums\RemainderStatus;
use App\Models\DiscordUser;
use Illuminate\Http\Response;
use App\Http\Requests\Api\v1\StoreRemainderRequest;
use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\Remainder;

/**
 * Creates a remainder
 */
class CreateRemainderAction
{

    /**
     * Creates a remainder with validated data
     *
     * @param StoreRemainderRequest $request The validated request
     * @param DiscordUser $discordUser The DiscordUser owner of the Remainder
     *
     * @return Response [201] Returns the created Remainder
     *
     */
    public static function run(StoreRemainderRequest $request, DiscordUser $discordUser): Response
    {
        $remainder = Remainder::create(array_merge(
            $request->validated(),
            [
                'discord_user_id' => $discordUser->id,
                'status' => RemainderStatus::NEW->value,
            ]
        ));

        return response(
            content: [
                'data' => RemainderResource::make($remainder),
            ],
            status: 201
        );
    }

}
