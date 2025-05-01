<?php

namespace App\Actions;

use App\Models\Remainder;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

/**
 * Deletes a Remainder
 *
 * NOTE: only performs softdelete
 */
class DeleteRemainderAction
{

    /**
     * Deleted the Remainder
     *
     * @param Remainder $remainder The Remainder to delete
     *
     * @return Response|ResponseFactory [204] Returns an empty response
     *
     */
    public static function run(Remainder $remainder): Response|ResponseFactory
    {
        $remainder->delete();

        return response(status: 204);
    }

}
