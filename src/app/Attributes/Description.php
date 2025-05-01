<?php

namespace App\Attributes;

#[\Attribute]
class Description
{
    public function __construct(
        public string $description,
    ) {}
}
