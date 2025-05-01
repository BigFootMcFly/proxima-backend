<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscordUserRemainderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return array_merge(
            DiscordUserResource::make($this)->toArray($request),
            ['remainders' => $this->remainders->toArray($request)]
        );
    }



}
