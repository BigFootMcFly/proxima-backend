<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreDiscordUserRequest;
use App\Http\Requests\Api\v1\UpdateDiscordUserRequest;
use App\Http\Requests\Api\v1\DeleteDiscordUserRequest;
use App\Http\Resources\Api\v1\DiscordUserResource;
use App\Models\DiscordUser;
use App\Actions\DeleteDiscordUserAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @group Discord User Managment
 *
 * APIs to manage [DiscordUser](#discorduser) records.
 *
 * These endpoints can be used to Query/Update/Delete [DiscordUser](#discorduser) records.
 */
class DiscordUserController extends Controller
{
    /**
     * List DiscorUsers.
     *
     * Paginated list of [DiscordUser](#discorduser) records.
     *
     * @queryParam page_size int Items per page. Defaults to 100. Example: 25
     * @queryParam page int Page to query. Defaults to 1. Example: 1
     * @response 200 scenario=success {"data":[{"id":1,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"hu_HU","timezone":"Europe/Budapest"},{"id":6,"snowflake":"860046989130727450","user_name":"Teszt Elek","global_name":"holnap_is_teszt_elek","locale":"hu","timezone":"Europe/Budapest"},{"id":12,"snowflake":"112233445566778899","user_name":"Igaz Álmos","global_name":"almos#1244","locale":null,"timezone":null}],"meta":{"current_page":1,"from":1,"last_page":1,"per_page":10,"to":3,"total":3}}
     *
     * @param \Illuminate\Http\Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        return DiscordUserResource::collection(DiscordUser::paginate(perPage: $request->page_size ?? 100));
    }

    /**
     * Create a new DiscordUser record.
     *
     * Creates a new [DiscordUser](#discorduser) record with the provided parameters.
     *
     * @bodyParam snowflake string required A valid [snowflake](#snowflake). Example: 481398158916845568
     * @bodyParam locale string A valid [locale](#locale). Example: hu_HU
     * @bodyParam user_name string The user_name registered in Discord. Example: bigfootmcfly
     * @bodyParam global_name string The global_name registered in Discord. Example: BigFoot McFly
     * @bodyParam avatar string The avatar url registered in Discord. No-example
     * @bodyParam timezone string A valid [time zone](#timezone). Example: Europe/Budapest
     * @response 200 scenario=success {"data":{"id":42,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"hu_HU","timezone":"Europe\/Budapest"}}
     * @response 422 scenario="Unprocessable Content" {"errors":{"snowflake":["The snowflake has already been taken."]}}
     */
    public function store(StoreDiscordUserRequest $request)
    {
        $discordUser = DiscordUser::create($request->validated());
        return response(
            content: [
                'data' => DiscordUserResource::make($discordUser),
            ],
            status: 201
        );
    }

    /**
     * Get the specified DiscordUser record.
     *
     * Returns the specified [DiscordUser](#discorduser) record.
     *
     * @urlParam id int required [DiscordUser](#discorduser) ID. Example: 42
     * @response 200 scenario=success {"data":{"id":42,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"hu_HU","timezone":"Europe\/Budapest"}}
     *
     * @return DiscordUserResource
     *
     */
    public function show(DiscordUser $discordUser)
    {
        return DiscordUserResource::make($discordUser);
    }

    /**
     * Update the specified DiscordUser record.
     *
     * Updates the specified [DiscordUser](#discorduser) with the supplied values.
     *
     * @urlParam id int required [DiscordUser](#discorduser) ID. Example: 42
     * @bodyParam snowflake string required The snowflake of the [DiscordUser](#discorduser) to update. Example: 481398158916845568
     * @bodyParam user_name string The user_name registered in Discord. No-example
     * @bodyParam global_name string The global_name registered in Discord. No-example
     * @bodyParam avatar string The avatar url registered in Discord. No-example
     * @bodyParam locale string A valid locale. <a href="https://github.com/Nerdtrix/language-list/blob/main/language-list-json.json" target="_blank">Locale list (json)</a> No-example
     * @bodyParam timezone string A valid [time zone](#timezone). Example: Europe/London
     * @response 200 {"data":{"id":42,"snowflake":"481398158916845568","user_name":"bigfootmcfly","global_name":"BigFoot McFly","locale":"hu_HU","timezone":"Europe\/London"}}
     * @response 422 scenario="Unprocessable Content" {"errors":{"snowflake":["Invalid snowflake"]}}
     */
    public function update(UpdateDiscordUserRequest $request, DiscordUser $discordUser)
    {
        //NOTE: due to filament validation, the snowflake is not checked in the request, it must be checked here.
        $snowflake = $request->input('snowflake');
        if ($discordUser->snowflake !== $snowflake) {
            return response(
                content: [
                    "errors" => [
                        "snowflake" => [
                            "Invalid snowflake",
                        ],
                    ],
                ],
                status: 422
            );
        }

        $original = DiscordUser::where('snowflake', $snowflake)->first();

        $discordUser->update($request->validated());

        return response(
            content: [
                'data' => (DiscordUserResource::make($discordUser)),
                'changes' => $discordUser->getChangedValues($original, ['updated_at']),
            ],
            status: 200
        );

    }

    /**
     * Remove the specified DiscordUser record.
     *
     * Removes the specified [DiscordUser](#discorduser) record **with** all the [Remainder](#remainder) records belonging to it.
     *
     * @urlParam id int required [DiscordUser](#discorduser) ID. Example: 42
     * @bodyParam snowflake string required The snowflake of the [DiscordUser](#discorduser) to delete. Example: 481398158916845568
     * @response 204
     *
     * @param \App\Http\Requests\Api\v1\DeleteDiscordUserRequest $request
     * @param \App\Models\DiscordUser $discordUser
     *
     */
    public function destroy(DeleteDiscordUserRequest $request, DiscordUser $discordUser)
    {
        return DeleteDiscordUserAction::run($discordUser);
    }
}
