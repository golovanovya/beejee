<?php

namespace App\Route;

use League\Route\Router as LeagueRouter;

class Router extends LeagueRouter
{
    /**
     * {@inheritdoc}
     */
    public function dispatch(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        if ($this->getStrategy() === null) {
            $this->setStrategy(new ApplicationStrategy());
        }

        $this->prepRoutes($request);
        
        /** @var Dispatcher $dispatcher */
        $dispatcher = (new Dispatcher($this->getData()))->setStrategy($this->getStrategy());

        foreach ($this->getMiddlewareStack() as $middleware) {
            if (is_string($middleware)) {
                $dispatcher->lazyMiddleware($middleware);
                continue;
            }

            $dispatcher->middleware($middleware);
        }

        return $dispatcher->dispatchRequest($request);
    }
}
