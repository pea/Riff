<?php

namespace Riff\User;

use Respect\Validation\Validator as v;
use CaseHelper\CaseHelperFactory;
use WP_User;

class User
{
    public $description;
    public $display_name;
    public $first_name;
    public $ID;
    public $last_name;
    public $meta;
    public $user_email;
    public $user_login;
    public $user_nicename;
    public $user_pass;
    public $user_url;

    public $user;
    public $errors = [];

    public $validation = [
        'description' => [],
        'display_name' => [],
        'first_name' => ['notEmpty', 'alpha'],
        'ID' => ['digit'],
        'last_name' => ['notEmpty', 'alpha'],
        'user_email' => ['notEmpty', 'email'],
        'user_login' => [],
        'user_nicename' => [],
        'user_pass' => ['notEmpty', 'password'],
        'user_url' => ['url']
    ];

    public $titles = [
        'description' => 'Description',
        'display_name' => 'Display Name',
        'first_name' => 'First Name',
        'ID' => 'ID',
        'last_name' => 'Last Name',
        'user_email' => 'Email',
        'user_login' => 'Username',
        'user_nicename' => 'Nice Name',
        'user_pass' => 'Password',
        'user_url' => 'Website Url'
    ];

    public function __construct()
    {
        $className = basename(get_class($this), 'User');
        $roleName = explode('\\', $className);

        $ch = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_PASCAL_CASE);
        $this->roleSlug = strtolower($ch->toSnakeCase(end($roleName)));
        $this->roleName = ucwords($ch->toSpaceCase(end($roleName)));

        if (!get_role($this->roleName)) {
            add_role(
                $this->roleSlug,
                $this->roleName,
                [
                    'read'         => false,
                    'edit_posts'   => false,
                    'delete_posts' => false,
                ]
            );
        }
    }

    /**
     * Insert or update the user
     * @return void
     */
    public function save()
    {
        
        // Update an existing user
        if (isset($this->ID)) {
            $this->validate(true);
            if (empty($this->errors)) {
                $this->updateUser();
            }
        }

        // Create a new user
        if (!isset($this->ID)) {
            $this->validate();
            if (empty($this->errors)) {
                $response = wp_create_user($this->user_email, $this->user_pass, $this->user_email);

                if (is_int($response)) {
                    $this->ID = $response;
                    $user = new WP_User($this->ID);
                    $user->add_role($this->roleName);
                    $this->updateUser();
                }

                if (!is_int($response)) {
                    $createUserErrors = array_map(function ($item) {
                        return $item[0];
                    }, $response->errors);
                    $this->errors = array_merge($this->errors, $createUserErrors);
                }
            }
        }
    }

    /**
     * Update the user
     * @return void
     */
    public function updateUser()
    {
        $this->user = [
            'description' => $this->description,
            'display_name' => $this->display_name,
            'first_name' => $this->first_name,
            'ID' => $this->ID,
            'last_name' => $this->last_name,
            'user_email' => $this->user_email,
            'user_login' => $this->user_login,
            'user_nicename' => $this->user_nicename,
            'user_pass' => $this->user_pass,
            'user_url' => $this->user_url,
            'role' => $this->roleSlug
        ];

        $updateUserResponse = wp_update_user($this->user);
        if (!empty($this->meta)) {
            foreach ($this->meta as $key => $item) {
                if (!isset($item['value']) && empty($item['value'])) {
                    $item['value'] = get_user_meta($this->ID, $key, true);
                }
                if (isset($item['value']) && empty($item['value'])) {
                    $item['value'] = '';
                }
                if (isset($item['value']['tmp_name'])) {
                    $item['value'] = $this->uploadFile($item['value']);
                }
                update_user_meta($this->ID, $key, $item['value']);
                $this->user['meta'][$key] = $item['value'];
            }
        }
    }

    public function validate($updating = false)
    {
        // Validate Wordpress fields
        foreach ($this->validation as $key => $rules) {
            foreach ($rules as $rule) {
                $validation = $this->$rule(
                    $this->titles[$key],
                    $this->$key
                );

                // If we're updating a user only validate data we're looking to change
                if ($updating && !isset($this->$key)) {
                    continue;
                }

                // If the field isn't present or required don't run other validation rules on it
                if (empty($this->$key) && !in_array('notEmpty', $this->validation[$key])) {
                    continue;
                }

                if ($validation) {
                    $ch = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_SPACE_CASE);
                    $this->errors[$ch->toSnakeCase($this->titles[$key])] = $validation;
                }
            }
        }

        // Validate custom meta data
        foreach ($this->meta as $key => $meta) {
            foreach ($meta['rules'] as $rule) {
                $validation = $this->$rule($meta['title'], @$meta['value']);

                // If we're updating a user only validate data we're looking to change
                if ($updating && !isset($meta['value'])) {
                    continue;
                }

                // If the meta field isn't present or required don't run other validation rules on it
                if (empty($meta['value']) && !in_array('notEmpty', $meta['rules'])) {
                    continue;
                }

                // // If the meta file isn't present or required don't run other validation rules on it
                if (isset($meta['value']['tmp_name']) && empty($meta['value']['tmp_name']) && !in_array('notEmpty', $meta['rules'])) {
                    continue;
                }
                
                if ($validation) {
                    $ch = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_SPACE_CASE);
                    $this->errors[$ch->toSnakeCase($meta['title']) . '_' . $ch->toSnakeCase($rule)] = $validation;
                }
            }
        }
    }

    /**
     * Upload a file
     * @param  array $file $_FILE
     * @return int Attachment id
     */
    public function uploadFile($file)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $upload = wp_handle_upload($file, ['test_form' => false]);
        if (!isset($upload['error'])) {
            $filename = $upload['file'];
            $filetype = wp_check_filetype(basename($filename), null);
            $attachment = [
                'guid' => wp_upload_dir()['url'] . '/' . basename($filename),
                'post_mime_type' => $filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            ];
            $attachmentId = wp_insert_attachment($attachment, $filename);
            return $attachmentId;
        }
    }

    /**
     * Validation Rules
     * @param  string $name  Human readable name of input
     * @param  * $value Value of input
     * @return void
     */
    public function notEmpty($name, $value)
    {
        if (empty($value)) {
            return $name . ' can\'t be empty';
        }
    }

    public function url($name, $value)
    {
        if (!v::url()->validate($value)) {
            return $name . ' should be a valid url';
        }
    }

    public function email($name, $value)
    {
        if (!v::email()->validate($value)) {
            return $name . ' should be a valid email';
        }
    }

    public function password($name, $value)
    {
        if (!v::stringType()->length(6, null)->validate($value)) {
            return $name . ' should be at least 6 characters long';
        }
    }

    public function phone($name, $value)
    {
        if (!v::phone()->validate($value)) {
            return $name . ' should be a valid phone number';
        }
    }

    public function alpha($name, $value)
    {
        if (!v::alpha()->validate($value)) {
            return $name . ' should contain only alphabetical characters';
        }
    }

    public function digit($name, $value)
    {
        if (!v::digit()->validate($value)) {
            return $name . ' should be a digit';
        }
    }
}
