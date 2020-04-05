<?php

namespace App\Template;

function render($template, $params)
{
    extract($params);
    ob_start();
    include($template);
    return ob_get_clean();
}