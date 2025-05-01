<?php

namespace App\Traits;

trait FilamentFormRequest
{
    /**
     * Converts complex rules to usable versions for filament
     *
     * @return array list of rules
     *
     */
    public static function asFilamentRules(): array
    {
        //NOTE: currently all special rules are removed from the project, this is here for compatibility
        //NOTE: filament may not know the table name in a 'unique' filter, so a 'unique' should be converted to 'unique:table_name'
        //NOTE: In:: rules should be converted to array ( '->__toString()' )

        /* @phpstan-ignore new.static (Symfony package) */
        return (new static())->rules();
    }
}
