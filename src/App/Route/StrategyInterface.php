<?php

namespace App\Route;

use League\Route\Strategy\StrategyInterface as BasicStrategyInterface;

interface StrategyInterface extends BasicStrategyInterface
{
    public function isPrependThrowableDecorator(): bool;
}
