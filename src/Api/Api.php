<?php

namespace Riff\Api;

class Api
{
    public function __construct()
    {
        if (function_exists('get_blog_details')) {
            $siteUrl = get_blog_details(get_current_blog_id());
            $siteUrl = $siteUrl->path;
        } else {
            $siteUrl = get_bloginfo('url');
        }

        $classPath = explode('\\', get_class($this));

        $className = end($classPath);
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $reqName = end($uri);
        if (($uri[1] == 'api' || (str_replace('/', '', $siteUrl) == $uri[1] && @$uri[2] == 'api')) &&
            strtolower($reqName) == strtolower($className)
        ) {
            $this->init();
            exit;
        }
    }
}
