<?php

namespace App\Filter\Modifier\Factory;

use App\Filter\Modifier\PriceModifierInterfece;

interface PriceModifierFactoryInterface
{
    const PRICE_MODIFIER_NAMESPACE = 'App\Filter\Modifier\\';

    public function create(string $modifierType): PriceModifierInterfece;
}