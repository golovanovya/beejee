<?php

namespace App\Middleware;

use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class HandleHttpError implements MiddlewareInterface
{
    protected $layout = 'layout/main';
    protected $view;
    protected $error;
    protected $templateRenderer;


    public function __construct(Throwable $error, Engine $templateRenderer, string $view)
    {
        $this->error = $error;
        $this->templateRenderer = $templateRenderer;
        $this->view = $view;
    }
    
    public function render(string $view, array $data = []): ResponseInterface
    {
        $template = $this->templateRenderer->make($view);
        $template->layout($this->layout, $data);
        $response = new Response();
        $response->getBody()
            ->write(
                $template->render($data)
            );
        return $response;
    }
    
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->render($this->view, [
            'e' => $this->error,
            'notice' => $request->getAttribute('notice'),
            'isAdmin' => $request->getAttribute('user') !== null,
        ]);
    }
}
