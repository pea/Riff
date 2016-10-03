<?php

namespace Riff\PostType;

use Inflect\Inflect;

use WP_POST;
use WP_Query;

class PostType
{
    public function __construct()
    {

        $postTypeName = basename(get_class($this), 'PostType');
        $postTypeName = explode('\\', $postTypeName);
        $this->postTypeName = end($postTypeName);

        add_action('after_switch_theme', [ &$this, 'themeActivation' ]);
        add_action('cmb2_init', [ &$this, 'init' ]);
        add_action('the_post', [ &$this, 'preparePost'], 10, 1);
    }

    public function themeActivation()
    {
        $this->init();
        flush_rewrite_rules();
    }

    public function init()
    {

        if(!post_type_exists(strtolower(Inflect::singularize($this->postTypeName)))) {
            $labels = [
                'name' => __(Inflect::pluralize($this->postTypeName)),
                'singular_name' => __(Inflect::singularize($this->postTypeName)),
                'menu_name' => __(Inflect::pluralize($this->postTypeName)),
                'add_new' => __('Add ' . Inflect::singularize($this->postTypeName)),
                'add_new_item' => __('Add ' . Inflect::singularize($this->postTypeName)),
                'edit_item' => __('Edit ' . Inflect::singularize($this->postTypeName)),
                'new_item' => __('Add ' . Inflect::singularize($this->postTypeName)),
                'view_item' => __('View'),
                'search_items' => __('Search ' . Inflect::pluralize($this->postTypeName)),
                'not_found' => __('No ' . strtolower(Inflect::pluralize($this->postTypeName)) . ' found'),
                'not_found_in_trash' => __('No ' . strtolower(Inflect::pluralize($this->postTypeName)) . ' found in trash')
            ];
            
            $options = [
                'labels' => $labels,
                'public' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'capability_type' => 'post',
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug' => strtolower(Inflect::pluralize($this->postTypeName)),
                    'hierarchical' => true,
                    'with_front' => false
                ],
                'hierarchical' => true,
                'has_archive' => true
            ];
            
            $options = apply_filters(strtolower(Inflect::singularize($this->postTypeName)) . 'Options', $options);
            
            register_post_type(strtolower(Inflect::singularize($this->postTypeName)), $options);
        }
    }

    public function preparePost(WP_POST $post)
    {
        $post->meta = $this->getMeta($post);
        return $post;
    }

    public function getMeta($post)
    {
        $postMeta = get_post_meta($post->ID, '', true);
        foreach ($postMeta as $key => $meta) {
            $postMeta[$key] = count($meta) > 1 ? $meta : $meta[0];
        }
        return $postMeta;
    }
}
