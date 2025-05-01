![Docker build](https://proxima.goliath.hu/proxima/backend/actions/workflows/testing.yaml/badge.svg)
![Docker build](https://proxima.goliath.hu/proxima/backend/actions/workflows/latest.yaml/badge.svg)

![Proxima Discord bot](../res/logo.svg)


## The source code of the backend.

This is just a brief list of the structure and functionality for the source tree, the default, mianly unchanged laravel filles will not be covered by this list.

The source files are well documented, fell free to read trough them.

- [app](app)<br>
    The laravel application.

    - [Actions](app/Actions)<br>
        Custom actions used by the application.

    - [Attributes](app/Attributes)<br>
        Custom attributes used by the application.

    - [Enums](app/Enums)<br>
        Enums to ensure data integrity.

    - [Filament](app/Filament)<br>
        The definitions for the admi UI.

    - [Filament](app/Filament)<br>
        The definitions for the admi UI.

    - [Helpers](app/Helpers)<br>
        Some helper functions for convenience.

    - [Http/Controllers](app/Http/Controllers)<br>
        The controllers to handle all web/api requests

        - [Http/Controllers](app/Http/Controllers)<br>
            The controllers to handle all web/api requests

        - [Http/Controllers/Api/v1](app/Http/Controllers/Api/v1)<br>
            The controllers to handle the API requests

    - [Http/Middleware](app/Http/Middleware)<br>
        The custom middleware to handle all web/api requests

        - [HardenHeaders.php](app/Http/Middleware/HardenHeaders.php)<br>
            Hides some unnecessary informations from the bots.

        - [StripPaginationInfo.php](app/Http/Middleware/StripPaginationInfo.php)<br>
            Removes web links from API paginated responses.

    - [Http/Requests](app/Http/Requests)<br>
        Custom request classes to validate input data for all requests.

    - [Http/Resources/Api/v1](app/Http/Resources/Api/v1)<br>
        Custom resource classes to return only the needed data for the API calls.

    - [Http/Livewire](app/Http/Livewire)<br>
        One lonly custom Livewire component, that was missing from filament.

    - [Http/Models](app/Http/Models)<br>
        The models used by the application.

    - [Http/Providers](app/Http/Providers)<br>
        The service providers for the application.

    - [Http/Traits](app/Http/Traits)<br>
        Common code used by multiple classes/enums.

    - [Http/Validators/SnowflakeValidator.php](app/Http/ValidatorsSnowflakeValidator.php)<br>
        Minimalistic validator for discord snaowflake.

- [database](database)<br>
    Database migrations, factories, seeders.

- [public/docs](public/docs)<br>
    The documentation for the API, created by <a href="https://scribe.knuckles.wtf/laravel" target="_blank">Scribe</a><br>
    The full API documentation is available <a href="proxima.goliath.hu/docs" target="_blank">here</a>.

- [routes/api.php](routes/api.php)<br>
    The routes the API access.

- [routes/console.php](routes/console.php)<br>
    The custom inline commands for the application.

- [tests](tests)<br>
    Feature tests for the application.
