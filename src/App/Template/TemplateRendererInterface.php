<?php

namespace App\Template;

/**
 * Render view with layout
 */
interface TemplateRendererInterface extends RenderableInterface
{
    /**
     * Set the template's layout
     * @param string $layout
     * @param array $data
     */
    public function layout(string $layout, array $data = []);
}
