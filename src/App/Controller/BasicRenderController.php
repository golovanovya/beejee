<?php

namespace App\Controller;

use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BasicRenderController implements RendarableControllerInterface
{
    /**
     * @var Engine
     */
    private $templateRenderer;
    /**
     * @var \App\User
     */
    private $user;
    /**
     * @var \App\Notice
     */
    private $notice;
    /**
     * @var string
     */
    private $csrf;


    protected $layout = 'layout/main';
    
    public function __construct(Engine $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }
    
    public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $this->user = $request->getAttribute('user');
        $this->notice = $request->getAttribute('notice');
        $this->csrf = $request->getAttribute('csrf');
        return $this->action($request, $args);
    }
    
    public function render(string $view = 'app/index', array $data = array()): ResponseInterface
    {
        $data['isAdmin'] = $this->user !== null;
        $data['notice'] = $this->notice;
        $data['csrf'] = $this->csrf;
        $template = $this->templateRenderer->make($view);
        $template->layout($this->layout, $data);
        $response = new Response();
        $response->getBody()
            ->write(
                $template->render($data)
            );
        return $response;
    }
}
