<?php

namespace App\Http\Controllers\Api\v1;

use App\Actions\CreateRemainderAction;
use App\Actions\DeleteRemainderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\DeleteRemainderRequest;
use App\Http\Requests\Api\v1\StoreRemainderRequest;
use App\Http\Requests\Api\v1\UpdateRemainderRequest;
use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\DiscordUser;
use App\Models\Remainder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @group Remainder Managment
 *
 * APIs to manage Remainders records.
 *
 * These endpoints can be used to Query/Update/Delete [Remainder](#remainder) records.
 *
 */

class DiscordUserRemaindersController extends Controller
{
    /**
     * List of Remainder records.
     *
     * Paginated list of [Remainder](#remainder) records belonging to the specified [DiscordUser](#discorduser).
     *
     * @urlParam discord_user_id int required [DiscordUser](#discorduser) ID. Example: 42
     * @queryParam page_size int Items per page. Defaults to 100. Example: 25
     * @queryParam page int Page to query. Defaults to 1. Example: 1
     * @response 200 scenario=success {"data":[{"id":38,"discord_user_id":42,"channel_id":null,"due_at":1736259300,"message":"Update todo list","status":"new","error":null},{"id":121,"discord_user_id":42,"channel_id":null,"due_at":1736259480,"message":"Water plants","status":"new","error":null}],"meta":{"current_page":1,"from":1,"last_page":1,"per_page":25,"to":2,"total":2}}
     *
     * @param DiscordUser $discordUser
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(DiscordUser $discordUser, Request $request): ResourceCollection
    {
        return RemainderResource::collection($discordUser->remainders()->paginate(perPage: $request->page_size ?? 100));
    }

    /**
     * Create a new Remainder record.
     *
     * Creates a new [Remainder](#remainder) record with the provided parameters.
     *
     * @urlParam discord_user_id int required [DiscordUser](#discorduser) ID. Example: 42
     *
     * @bodyParam due_at string required The "Due at" time ([timestamp](#timestamp)) of the remainder Example: 1732550400
     * @bodyParam message string required The message to send to the discord user. Example: Check maintance results!
     * @bodyParam channel_id string The [snowflake](#snowflake) of the channel to send the remainder to. No-example
     *
     * @response 200 {"data":{"id":18568,"discord_user_id":42,"channel_id":null,"due_at":1732550400,"message":"Check maintance results!","status":"new","error":null}}
     *
     */
    public function store(StoreRemainderRequest $request, DiscordUser $discordUser)
    {
        return CreateRemainderAction::run($request, $discordUser);
    }

    /**
     * Update the specified Remainder record.
     *
     * Updates the specified [Remainder](#remainder) record with the provided parameters.
     *
     * @urlParam discord_user_id int required [DiscordUser](#discorduser) ID. Example: 42
     * @urlParam id int required [Remainder](#remainder) ID. Example: 18568
     * @bodyParam due_at string The "Due at" time ([timestamp](#timestamp)) of the remainder. No-example
     * @bodyParam message string The message to send to the discord user. No-example
     * @bodyParam channel_id string The [snowflake](#snowflake) of the channel to send the remainder to. No-example
     * @bodyParam error string Error description in case of failure. Example: Unknow user
     * @bodyParam status string Status of the [Remainder](#remainder).
     *
     * For possible values see: [RemainderStatus](#remainderstatus) Example: failed
     *
     * @response 200 {"data":{"id":18568,"discord_user_id":42,"channel_id":null,"due_at":1732550400,"message":"Check maintance results!","status":"failed","error":"Unknow user"},"changes":{"status":{"old":"new","new":"failed"},"error":{"old":null,"new":"Unknow user"}}}
     *
     */
    public function update(UpdateRemainderRequest $request, DiscordUser $discordUser, Remainder $remainder)
    {
        $original = Remainder::find($remainder->id)->first();
        $remainder->update($request->validated());

        return response(
            content: [
                'data' => (RemainderResource::make($remainder)),
                'changes' => $remainder->getChangedValues($original, ['updated_at']),
            ],
            status: 200
        );
    }

    /**
     * Remove the specified Remainder record.
     *
     * Removes the specified [Remainder](#remainder) record with the provided parameters.
     *
     * @urlParam discord_user_id int required [DiscordUser](#discorduser) ID. Example: 42
     * @urlParam id int required [Remainder](#remainder) ID. Example: 18568
     * @bodyParam snowflake string required The [snowflake](#snowflake) of the DiscordUser of the Remainder to delete. Example: 481398158916845568

     * @response 204
     *
     */
    public function destroy(DeleteRemainderRequest $request, DiscordUser $discordUser, Remainder $remainder)
    {
        return DeleteRemainderAction::run($remainder);
    }

}
