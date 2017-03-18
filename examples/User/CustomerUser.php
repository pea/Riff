<?php

namespace Theme\Common\User;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Riff\User\User;

class StudentUser extends User
{
    public $meta = [
        '_phone' => [
            'title' => 'Phone Number',
            'rules' => ['phone']
        ],
        '_display_picture' => [
            'title' => 'Display Picture',
            'rules' => ['displayPicture']
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        add_action('carbon_register_fields', [ $this, 'metaData']);
    }

    public function metaData()
    {
        Container::make('user_meta', 'User Details')
            ->show_on_user_role('customer')
            ->add_fields([
                Field::make('text', 'phone', 'Phone Number'),
                Field::make('file', 'display_picture', 'Display Picture'),
            ]);
    }

    public function displayPicture($name, $value)
    {
        // Mimes
        $mimes = [
            'image/png',
            'image/jpeg'
        ];
        if (!in_array($value['type'], $mimes)) {
            return $name . ' should either be PNG or JPG';
        }
        // Size
        $max = 1;  // MB
        if ($value['size'] > $max * 1000000) {
            return $name . ' should be smaller than ' . $max . 'MB';
        }
    }
}
