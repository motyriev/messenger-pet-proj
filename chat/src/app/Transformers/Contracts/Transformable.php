<?php

namespace App\Transformers\Contracts;

interface Transformable
{
    public static function transform(array $entity): array;
    public static function transformMany(array $entities): array;
}