<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RemainderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'discord_user_id' => $this->discord_user_id,
            'channel_id' => $this->channel_id,
            'due_at' => $this->due_at->timestamp,
            'message' => $this->message,
            'status' => $this->status,
            'error' => $this->error,
            ...(
                $request->has('withDiscordUser')
                ? ['discord_user' => new DiscordUserResource($this->discordUser)]
                : []
            ),
            //NOTE: the above could be done with this:
            //       it is easier to read, but then, the relation must be loaded before this call, so the Request must be handled before...
            /*
                'discord_user' => whenLoaded(new DiscordUserResource($this->discordUser)),
            */
        ];
    }

}
