<?php

namespace App\Template;

/**
 * Interface for renderer
 */
interface RenderableInterface
{
    /**
     * Render the template
     * @param string $view path to template
     * @param array $data
     * @return string
     */
    public function render(string $view, array $data = []): string;
}
