<?php

namespace App\Route;

use League\Route\Dispatcher as LeagueDispatcher;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;

class Dispatcher extends LeagueDispatcher
{
    /**
     * {@inheritdoc}
     */
    protected function setNotFoundDecoratorMiddleware(): void
    {
        $strategy = $this->getStrategy();
        $middleware = $strategy->getNotFoundDecorator(new NotFoundException());
        if ($strategy instanceof StrategyInterface && $strategy->isPrependThrowableDecorator() === false) {
            $this->middleware($middleware);
        } else {
            parent::setNotFoundDecoratorMiddleware();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setMethodNotAllowedDecoratorMiddleware(array $allowed): void
    {
        $strategy = $this->getStrategy();
        $middleware = $strategy->getMethodNotAllowedDecorator(
            new MethodNotAllowedException($allowed)
        );
        if ($strategy instanceof StrategyInterface && $strategy->isPrependThrowableDecorator() === false) {
            $this->middleware($middleware);
        } else {
            parent::setMethodNotAllowedDecoratorMiddleware($allowed);
        }
    }
}
