
# Classes

## DiscordUser

This record stores the general informations of a discord user.

### Fields

- **id** - *The unique ID of the DiscordUser record.*
- **snowflake** - *The unique snowflake of the discord user.*
- **user_name** - *The name of the discord user.*
- **global_name** - *The global name of the discord user.*
- **locale** - *The locale of the discord user.*
- **timezone** - *The timezone of the discord user.*

> Exaple:

```json
{
    "id": 42,
    "snowflake": "481398158916845568",
    "user_name": "bigfootmcfly",
    "global_name": "BigFoot McFly",
    "locale": "hu_HU",
    "timezone": "Europe/Budapest"
}
```

## Remainder

### Fields

- **id** - *The unique ID of the Remainder record.*
- **discord_user_id** - *The internal ID of the [DiscordUser](#discorduser).*
- **channel_id** - *The snowflake of the channel the remainder should be sent to.*
- **due_at** - *The "Due at" time ([timestamp](#timestamp)) of the remainder.*
- **message** - *The message to send to the discord user.*
- **status** - *The [status](#remainderstatus) of the remainder.*
- **error** - *The error (if any) that caused the remainder to fail.*

> Example:

```json
{
    "id": 18568,
    "discord_user_id": 42,
    "channel_id": null,
    "due_at": 1732950700,
    "message": "Maintance completed.",
    "status": "new",
    "error": null
}
```

# Types

## Timestamp

It measures time by the number of non-leap seconds that have elapsed since 00:00:00 UTC on 1 January 1970, the Unix epoch.

See: <a href="https://en.wikipedia.org/wiki/Unix_time" target="_blank">Unix time on Wikipedia</a>

See: <a href="https://www.unixtimestamp.com/" target="_blank">Timestamp converter</a>

## Snowflake

A unique identifier within the discord namespace.

See: <a href="https://discord.com/developers/docs/reference#snowflakes" target="_blank">Discord reference #snowflakes</a>

## Locale

A valid local identifier.

<a href="https://github.com/Nerdtrix/language-list/blob/main/language-list-json.json" target="_blank">Locale list (json)</a>

## Timezone

A valid timezone.

<a href="https://en.wikipedia.org/wiki/List_of_tz_database_time_zones" target="_blank">Timezone list</a>

## RemainderStatus

The status of a remainder.

Available values:

- **new** - *New remainder.*
- **failed** - *Something went wrong, will not try to resend it.*
- **pending** - *The remainder is currently being processed.*
- **deleted** - *The remainder was deleted.*
- **finished** - *The remainder finished succesfully.*
- **cancelled** - *The remainder was cancelled.*



# Error responses

## Unauthorized (401)

In case no authentication is provided or the authentication fails, a **`401, Unauthorized`** response is returned.

> Exa
mple response (401, Unauthorized):

```json
{
    "message": "Unauthenticated."
}
```

## Not Found (404)

If the requested record cannot be found, a **`404, Not Found`** response is returned.

> Example response (404, Not Found):

```json
{
    "message": "Not Found."
}
```

## Unprocessable Content (422)

If the request cannot be fulfilled, a **`422, Unprocessable Content`** response is returned.

See the returned message for details about the failure.

This is mostly due to **`validation errors`**.

> Example response (422, Unprocessable Content):

```json
{
    "errors": {
        "snowflake": [
            "Invalid snowflake"
        ]
    }
}
```

## Internal Server Error (500)

An internal server error occured. Please try again later or contact the operator.

> Example response (500, Internal Server Error):

```json
{
    "message": "Server Error"
}
```
