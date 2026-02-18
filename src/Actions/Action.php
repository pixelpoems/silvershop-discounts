<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Actions;

abstract class Action
{
    abstract public function perform();

    abstract public function isForItems();
}
