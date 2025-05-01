<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

trait HasChanges
{
    /**
     * Returns the list of changed fields with old/new values of a Model
     *
     * @param Model|null $original the Model befor the changes applied for $this (if $this is just created, this will be null)
     * @param array $hiddenFields list of fields to hide from teh result
     *
     * @return array
     *
     *  result example:
     *  [
     *      'name' => [
     *          'old' => 'Old Shatterhand'
     *          'new' => 'Lex Barker'
     *      ]
     *  ]
     *
     */
    public function getChangedValues(?Model $original = null, array $hiddenFields = []): array
    {
        $changes = [];

        foreach ($this->getChanges() as $key => $value) {

            if (in_array($key, $hiddenFields)) {
                continue;
            }

            $isDateTime = $this?->$key && is_object($this?->$key) && $this->$key::class === Carbon::class;

            $changes[$key] = [
                'old' => $isDateTime ? $original?->$key->timestamp : $original?->$key,
                'new' => $isDateTime ? Carbon::parse($value)->getTimestamp() : $value,
            ];
        }

        return $changes;
    }
}
