<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\Remainder;
use Carbon\Carbon;

/**
 * @group Remainder by DueAt Managment
 *
 * API to get Remainder records.
 *
 * This endpoint can be used to Query the actual [Remainder](#remainder) records.
 *
 */

class RemainderByDueAtController extends Controller
{
    /**
     * Returns all the "actual" reaminders for the given second.
     *
     * @urlParam due_at int required The time ([timestamp](#timestamp)) of the requested remainders. Example: 1735685999
     * @queryParam page_size int Items per page. Defaults to 100. Example: 25
     * @queryParam page int Page to query. Defaults to 1. Example: 1
     * @response 200 scenario=success {"data":[{"id":56,"discord_user_id":42,"channel_id":null,"due_at":1735685999,"message":"Update conatiner registry!","status":"new","error":null},{"id":192,"discord_user_id":47,"channel_id":null,"due_at":1735685999,"message":"Get some milk","status":"new","error":null}],"meta":{"current_page":1,"from":1,"last_page":1,"per_page":100,"to":2,"total":2}}
     */
    public function __invoke(int|string $due_at)
    {
        return RemainderResource::collection(Remainder::query()
            ->where('due_at', Carbon::createFromTimestamp($due_at))
            ->where('status', 'new')
            ->paginate(perPage: $request->page_size ?? 100));
    }
}
