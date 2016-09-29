<?php

namespace Riff\PostType;

use Inflect\Inflect;
use WP_POST;

class PostType
{
    public function __construct()
    {
        add_action('after_switch_theme', [ &$this, 'themeActivation' ]);
        add_action('cmb2_init', [ &$this, 'init' ]);
    }

    public function themeActivation()
    {
        $this->init();
        flush_rewrite_rules();
    }

    public function init()
    {

        $postTypeName = basename(get_class($this), 'PostType');
        $postTypeName = explode('\\', $postTypeName);
        $postTypeName = end($postTypeName);

        $labels = [
            'name' => __(Inflect::pluralize($postTypeName)),
            'singular_name' => __($postTypeName),
            'menu_name' => __(Inflect::pluralize($postTypeName)),
            'add_new' => __('Add ' . $postTypeName),
            'add_new_item' => __('Add ' . $postTypeName),
            'edit_item' => __('Edit ' . $postTypeName),
            'new_item' => __('Add ' . $postTypeName),
            'view_item' => __('View'),
            'search_items' => __('Search ' . Inflect::pluralize($postTypeName)),
            'not_found' => __('No ' . strtolower(Inflect::pluralize($postTypeName)) . ' found'),
            'not_found_in_trash' => __('No ' . strtolower(Inflect::pluralize($postTypeName)) . ' found in trash')
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
                'slug' => strtolower(Inflect::pluralize($postTypeName)),
                'hierarchical' => true,
                'with_front' => false
            ],
            'hierarchical' => true,
            'has_archive' => true
        ];
        
        $options = apply_filters(strtolower(Inflect::pluralize($postTypeName)) . 'Options', $options);
        
        register_post_type(strtolower(Inflect::pluralize($postTypeName)), $options);
    }
}
