<?php

namespace Theme\Common\Api;

use Riff\Api\Api;

class UserRegistration extends Api
{
    public function init()
    {
        $this->insert();
    }
    public function insert()
    {
        $request = $this->getRequest();
        $request['name'] = !empty($request['name']) ? $request['name'] : '';
        $request['email'] = !empty($request['email']) ? $request['email'] : '';
        $response = register_new_user($request['name'], $request['email']);
        echo json_encode($response);
    }
    public function getRequest()
    {
        return array_map(function ($item) {
            return sanitize_text_field($item);
        }, $_POST);
    }
}

new UserRegistration;
