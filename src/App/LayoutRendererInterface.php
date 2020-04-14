<?php

namespace App;

interface LayoutRendererInterface
{
    /**
     * Set the template's layout
     * @param string $layout
     * @param array $data
     */
    public function layout(string $layout, array $data = []);
}
