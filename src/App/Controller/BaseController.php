<?php

namespace App\Controller;

use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;

class BaseController
{
    protected $templateRenderer;

    public function __construct(Engine $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }
    
    public function render($view, $data = []): ResponseInterface
    {
        $response = new Response();
        $response->getBody()
            ->write(
                $this->templateRenderer->render($view, $data)
            );
        return $response;
    }
    
    protected function renderJson($data = [])
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        $response->withAddedHeader('content-type', 'application/json')->withStatus(200);
        return $response;
    }
}
