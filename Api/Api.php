<?php

namespace Riff\Api;

use WP_POST;
use WP_Query;

class Api
{
    public function __construct()
    {
        $classPath = explode('\\', get_class($this));
        $className = end($classPath);
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $reqName = end($uri);
        if ($uri[1] == 'api' && $reqName == $className) {
            $this->init();
            exit;
        }
    }

    public function init()
    {

    }
}
