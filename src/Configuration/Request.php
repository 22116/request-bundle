<?php

declare(strict_types=1);

namespace LSBProject\RequestBundle\Configuration;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class Request
{
    public function __construct(public ?Storage $storage = null)
    {
    }
}
