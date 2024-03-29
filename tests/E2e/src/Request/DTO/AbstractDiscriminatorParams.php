<?php

declare(strict_types=1);

namespace App\Request\DTO;

abstract class AbstractDiscriminatorParams implements \JsonSerializable
{
    public string $type;

    public function jsonSerialize()
    {
        return (array) $this;
    }
}
