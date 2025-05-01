<?php

namespace App\Enums;

use App\Attributes\Description;
use App\Traits\BackedEnumHelper;
use App\Traits\HasEnumDescription;

enum ApiPermission: string
{
    use BackedEnumHelper;
    use HasEnumDescription;

    #[Description('Handle discord users')]
    case ManageDiscordUsers = 'discord-users';

    #[Description('Handle Remainders')]
    case ManageDiscordUserRemainders = 'discord-user-remainders';

    #[Description('Get Discord User By Snowflake')]
    case ManageDiscordUserBySnowflake = 'discord-user-by-snowflake';

    #[Description('Get Remainders By Due At')]
    case GetRemaindersByDueAt = 'remainders-by-due-at';

}
