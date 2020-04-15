<?php

namespace App\Controller;

use App\Notice;
use App\Template\RenderableInterface;
use App\Template\TemplateRenderer;
use App\User;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BasicRenderController implements RendarableControllerInterface
{
    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Notice
     */
    private $notice;
    /**
     * @var string
     */
    private $csrf;

    /**
     * @var string default template layout
     */
    protected $layout = 'layout/main';
    
    public function __construct(RenderableInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }
    
    /**
     * Get values from request attributes for layout data
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $this->user = $request->getAttribute('user');
        $this->notice = $request->getAttribute('notice');
        $this->csrf = $request->getAttribute('csrf');
        return $this->action($request, $args);
    }
    
    /**
     * Render page and return html string
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render(string $view = 'app/index', array $data = []): string
    {
        $data['isAdmin'] = $this->user !== null;
        $data['notice'] = $this->notice;
        $data['csrf'] = $this->csrf;
        $this->templateRenderer->layout($this->layout, $data);
        return $this->templateRenderer->render($view, $data);
    }
    
    /**
     * Build response with body from string
     * @param string $string
     * @return ResponseInterface
     */
    public static function buildResponse(string $string): ResponseInterface
    {
        $response = new Response();
        $response->getBody()
            ->write($string);
        return $response;
    }
}
