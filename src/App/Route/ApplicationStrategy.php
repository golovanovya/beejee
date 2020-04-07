<?php

namespace App\Route;

use App\Middleware\HandleHttpError;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Strategy\ApplicationStrategy as Strategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ApplicationStrategy extends Strategy implements StrategyInterface
{
    /**
     * Need add throwable middleware to start or end middlewares stack
     * @return bool
     */
    public function isPrependThrowableDecorator(): bool
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface
    {
        return new HandleHttpError($exception, $this->getContainer()->get('templateRenderer'), 'app/http-error');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface
    {
        return new HandleHttpError($exception, $this->getContainer()->get('templateRenderer'), 'app/http-error');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        if ($this->getContainer()->get('debug') === true) {
            return parent::getThrowableHandler();
        }
        return new class ($this->getContainer()->get('templateRenderer'), 'app/error') extends HandleHttpError
        {
            protected $templateRenderer;
            protected $view;
            protected $error;
            
            public function __construct(\League\Plates\Engine $templateRenderer, string $view)
            {
                $this->templateRenderer = $templateRenderer;
                $this->view = $view;
            }

            /**
             * {@inheritdoc}
             *
             * @throws Throwable
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                try {
                    return $handler->handle($request);
                } catch (\League\Route\Http\Exception\HttpExceptionInterface $e) {
                    $this->error = $e;
                    $this->view = 'app/http-error';
                    return parent::process($request, $handler);
                } catch (Throwable $e) {
                    $this->error = $e;
                    return parent::process($request, $handler);
                }
            }
        };
    }
}
