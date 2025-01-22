<?php

namespace minichan\core;

class HtmlRenderer
{
    private string $dir;
    private array $vars;

    public function __construct(string $dir, array $vars = [])
    {
        $this->dir = $dir;
        $this->vars = $vars;
    }

    public function render(string $filename, array $vars = []): bool|string
    {
        ob_start();
        if (!empty($this->vars)) {
            extract($this->vars);
        }
        if (!empty($vars)) {
            extract($vars);
        }
        include $this->dir . '/' . $filename;
        return ob_get_clean();
    }
}
