<?php

namespace App\Traits;

use App\Attributes\Description;
use Illuminate\Support\Str;
use ReflectionEnum;

trait HasEnumDescription
{
    /**
     * Returns the description of the current enum case
     *
     * @return string The description of the case if defined, the value as headline otherwise
     *
     */
    public function description(): string
    {
        $ref = new ReflectionEnum($this);

        $case = $ref->getCase($this->name);

        $attr = $case->getAttributes(Description::class);

        if (count($attr) === 0) {
            return Str::headline($this->value);
        }

        return $attr[0]->newInstance()->description;

    }

    /**
     * Returns all the cases in 'value'=>'description' form
     *
     * @return array The value => description pairs of the cases
     *
     */
    public static function toDescribedArray(): array
    {

        //NOTE: the one-liner, a marvel of overengineering
        return array_reduce(
            array: self::cases(),
            callback: fn (array $carry, self $case): array =>
                $carry + [$case->value => $case->description()],
            initial: []
        );

        //NOTE: the wise way... (yields the same result)
        /*
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->description();
        }
        return $result;
        */
    }

}
