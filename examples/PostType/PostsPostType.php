<?php

/**
 * PostsPostType
 * Initiate post-type 'posts' and provide a place to
 * store methods relating to it.
 */

namespace Theme\Common\PostType;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Riff\PostType\PostType;
use Theme\Common\Helpers;

use WP_Post;
use WP_Query;

class PostsPostType extends PostType
{
    public function __construct()
    {
        add_action('carbon_register_fields', [ $this, 'metaBoxes' ]);
        parent::__construct();
    }

    /**
     * Initiate post type and custom methods
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Append meta data and custom data to $post
     * @param  WP_Post $post
     * @return $post object
     */
    public function preparePost(WP_Post $post)
    {
        $post = parent::preparePost($post);
        return $post;
    }
    
    /**
     * Metabox creation
     * @return void
     */
    public function metaBoxes()
    {
    }
}
