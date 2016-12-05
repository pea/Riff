<?php

namespace Riff\Api;

use WP_POST;
use WP_Query;

class Api
{
    public function __construct()
    {
        $currentBlog = get_blog_details(get_current_blog_id());
        $classPath = explode('\\', get_class($this));
        $className = end($classPath);
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $reqName = end($uri);
        if ($uri[1] == 'api' ||
            (str_replace('/', '', $uri) == $currentBlog['path'] && $uri[2] == 'api') &&
            $reqName == $className) {
            $this->init();
            exit;
        }
    }
}
