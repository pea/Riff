# API
An API for simple tasks is available. To make an endpoint, create the file `Common/Api/MyTask.php`. It will then be available at `http://example.com/api/MyTask`. The contents of this file might look like this:
```php
<?php

namespace Theme\Common;

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
```
Called with:
```
http://example.com/api/UserLogout
```

Note: If a more advanced API (with authentication) is required, use [WP Rest API](http://v2.wp-api.org/extending/adding/).