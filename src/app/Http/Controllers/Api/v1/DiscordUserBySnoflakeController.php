<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\UpdateDiscordUserRequest;
use Illuminate\Http\Request;
use App\Models\DiscordUser;
use App\Http\Resources\Api\v1\DiscordUserResource;

/**
 * @group Discord User By snowflake Managment
 *
 * APIs to manage DiscordUser records.
 *
 * These endpoints can be used to identify/create DiscordUser records identified by the [snowflake](#snowflake) that already exists in the discord app.
 *
 */

class DiscordUserBySnoflakeController extends Controller
{
    /**
     * Get the DiscordUser identified by the specified snowflake.
     *
     * Returns the [DiscordUser](#discorduser) record for the specified [snowflake](#snowflake), given in the url __discord_user_snowflake__ parameter.
     *
     * If it cannot be found, a [**404, Not Found**](#not-found-404) error is returned.
     *
     * @urlParam discord_user_snowflake string required A valid [snowflake](#snowflake). Example: 481398158916845568
     * @response 200 scenario=success {"data":{"id":42,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"hu_HU","timezone":"Europe\/Budapest"}}
     * @response 404 scenario="not found" {"message":"Not Found."}
     *
     */
    public function show(Request $request, DiscordUser $discordUser)
    {
        return DiscordUserResource::make($discordUser);
    }

    /**
     * Get _OR_ Update/Create the DiscordUser identified by the specified snowflake.
     *
     * If the record specified by the url __discord_user_snowflake__ parameter exists, it will be updated with the data provided in the body of the request.
     *
     * If it does not exists, it will be created using the given data.
     *
     * Returns the **updated/created** [DiscordUser](#discorduser) record.
     *
     * If anything goes wrong, a [**422, Unprocessable Content**](#unprocessable-content-422) error with more details will be returned.
     *
     * @urlParam snowflake string required A valid [snowflake](#snowflake). Example: 481398158916845568
     * @bodyParam snowflake string required A valid [snowflake](#snowflake). Example: 481398158916845568
     * @bodyParam locale string A valid [locale](#locale). Example: en_GB
     * @bodyParam user_name string The user_name registered in Discord. Example: bigfootmcfly
     * @bodyParam global_name string The global_name registered in Discord. Example: BigFoot McFly
     * @bodyParam avatar string The avatar url registered in Discord. No-example
     * @bodyParam timezone string A valid [time zone](#timezone). Example: Europe/London
     * @response 200 scenario=success {"data":{"id":42,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"en_GB","timezone":"Europe\/London"},"changes":{"locale":{"old":"hu_HU","new":"en_GB"},"timezone":{"old":"Europe\/Budapest","new":"Europe\/London"}}}
     * @response 422 scenario="Unprocessable Content" {"errors":{"snowflake":["The snowflake field is required."]}}
     *
     */
    public function update(UpdateDiscordUserRequest $request, int $snowflake)
    {

        $statusCode = match (($original = DiscordUser::where('snowflake', $snowflake)->first()) === null) {
            true => 201,
            false => 200
        };

        //NOTE: needed for the changeed field list, may be null
        $original = DiscordUser::where('snowflake', $snowflake)->first();

        $discordUser = DiscordUser::updateOrCreate(['snowflake' => $snowflake], $request->validated());

        return response([
            'data' => (DiscordUserResource::make($discordUser)),
            'changes' => $discordUser->getChangedValues($original, ['updated_at']),
        ], $statusCode);
    }


}
