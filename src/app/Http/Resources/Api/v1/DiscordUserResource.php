<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscordUserResource extends JsonResource
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
            'snowflake' => $this->snowflake,
            'user_name' => $this->user_name,
            'global_name' => $this->global_name,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
        ];
    }

}
