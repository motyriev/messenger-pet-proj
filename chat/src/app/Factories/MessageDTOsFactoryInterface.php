<?php

declare(strict_types=1);

namespace App\Factories;

interface MessageDTOsFactoryInterface
{
    public static function createFromArray(array $messages): array;
}