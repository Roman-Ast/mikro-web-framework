<?php

namespace App\Renderer;

use function App\Template\render as templateRender;

function render($filepath, $params = [])
{
    $templatepath = 'resources/views' . DIRECTORY_SEPARATOR . $filepath . '.phtml';
    return \App\Template\Render($templatepath, $params);
}