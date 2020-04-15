<?php

namespace App\Template;

/**
 * Render adapter for League Engine
 */
class TemplateRenderer implements TemplateRendererInterface
{
    private $engine;
    private $layout;
    private $layoutData = [];
    
    
    public function __construct(\League\Plates\Engine $templateEngine, string $layout)
    {
        $this->engine = $templateEngine;
        $this->layout = $layout;
    }
    
    /**
     * {@inheritdoc}
     */
    public function render(string $view, array $data = array()): string
    {
        /* @var $template \League\Plates\Template\Template */
        $template = $this->engine->make($view);
        $template->layout($this->layout, $this->layoutData);
        return $template->render($data);
    }
    
    /**
     * {@inheritdoc}
     */
    public function layout(string $layout, array $data = array())
    {
        $this->layout = $layout;
        $this->layoutData = $data;
    }
}
