<?php

namespace App\Controller;

use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;

class BaseController
{
    protected $isAdmin = false;
    protected $templateRenderer;

    public function __construct(Engine $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }
    
    protected function getSession(ServerRequestInterface $request)
    {
        $session = $request->getAttribute('session');
        $segment = $session->getSegment('jobController');
        $this->isAdmin = $segment->get('isAdmin');
        return $segment;
    }
    
    protected function renderJson($data = [])
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        $response->withAddedHeader('content-type', 'application/json')->withStatus(200);
        return $response;
    }
}
