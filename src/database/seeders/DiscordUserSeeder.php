<?php

namespace Database\Seeders;

use App\Models\DiscordUser;
use App\Models\Remainder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DiscordUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $discordUserCount = random_int(1_000, 2_000);
        $chunkSize = 2_000;

        // Creating Discord Users
        $discordUsers = DiscordUser::factory()->times($discordUserCount)->make(
            ['created_at' => Carbon::now()
                ->subSeconds(rand(0, 30 * 24 * 60 * 60))] // randomize to the last 30 days
        );

        // Saving Discord Users
        $chunks = $discordUsers->chunk($chunkSize);
        $chunks->each(function ($chunk) {
            DiscordUser::insert($chunk->toArray());
        });

        // Load Discord Users (with "id"-s already set)
        $discordUsers = DiscordUser::all();

        // Creating Remainders
        $remainders = new Collection();

        foreach ($discordUsers as $discordUser) {

            // Creating remainders for the current DiscordUser
            $discordUserRemainders = Remainder::factory()
                ->times(random_int(0, 30))
                ->make([
                    'discord_user_id' => $discordUser->id,
                    'created_at' => Carbon::now(),
                ]);

            // add remainder to buffer
            $remainders = $remainders->concat($discordUserRemainders);
            if ($remainders->count() >= $chunkSize) {
                // flush buffer if full
                Remainder::insert($remainders->toArray());
                $remainders = new Collection();
            }
        }

        // // flush buffer
        $chunks = $remainders->chunk(2000);
        $chunks->each(function ($chunk) {
            Remainder::insert($chunk->toArray());
        });

    }

}
