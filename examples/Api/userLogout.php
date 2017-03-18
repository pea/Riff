<?php

namespace Theme\Common\Api;

use Riff\Api\Api;

class UserLogout extends Api
{
    public function init()
    {
        wp_logout();
        echo '[]';
    }
}

new UserLogout;
