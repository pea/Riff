<?php

namespace Riff\PostType;

use CaseHelper\CaseHelperFactory;
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
        add_action('init', [ &$this, 'loadExistingPostType' ], 20, 1);
        add_action('init', [ &$this, 'init' ], 30, 1);
        add_action('the_post', [ &$this, 'preparePost'], 10, 1);
    }

    public function themeActivation()
    {
        $this->init();
        flush_rewrite_rules();
    }

    public function init()
    {
        if (!post_type_exists(strtolower($this->postTypeName))) {
            $singular = __(Inflect::singularize($this->camelToHumanCase($this->postTypeName)));
            $plural = __(Inflect::pluralize($this->camelToHumanCase($this->postTypeName)));

            $labels = [
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'add_new' => __('Add ' . $singular),
                'add_new_item' => __('Add ' . $singular),
                'edit_item' => __('Edit ' . $singular),
                'new_item' => __('Add ' . $singular),
                'view_item' => __('View'),
                'search_items' => __('Search ' . $plural),
                'not_found' => __('No ' . $plural . ' found'),
                'not_found_in_trash' => __('No ' . $plural . ' found in trash')
            ];
            
            $options = [
                'labels' => $labels,
                'public' => true,
                'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
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

    public function loadExistingPostType()
    {
        $postTypeParamCaseSingular = $this->camelToParamCase(Inflect::singularize($this->postTypeName));
        $postTypeSnakeCaseSingular = $this->camelToSnakeCase(Inflect::singularize($this->postTypeName));
        $postTypeParamCasePlural = $this->camelToParamCase(Inflect::pluralize($this->postTypeName));
        $postTypeSnakeCasePlural = $this->camelToSnakeCase(Inflect::pluralize($this->postTypeName));

        if (post_type_exists(strtolower($postTypeParamCaseSingular))) {
            $this->postTypeName = strtolower($postTypeParamCaseSingular);
        }

        if (post_type_exists(strtolower($postTypeSnakeCaseSingular))) {
            $this->postTypeName = strtolower($postTypeSnakeCaseSingular);
        }

        if (post_type_exists(strtolower($postTypeParamCasePlural))) {
            $this->postTypeName = strtolower($postTypeParamCasePlural);
        }

        if (post_type_exists(strtolower($postTypeSnakeCasePlural))) {
            $this->postTypeName = strtolower($postTypeSnakeCasePlural);
        }
    }

    public function preparePost(WP_POST $post)
    {
        $post->meta = $this->getMeta($post);
        $post->template = pathinfo(get_page_template_slug($post->ID), PATHINFO_FILENAME);
        return $post;
    }

    public function getMeta($post)
    {
        $postMeta = get_post_meta($post->ID, '', true);
        foreach ($postMeta as $key => $meta) {
            $postMeta[$key] = count($meta) > 1 ? maybe_unserialize($meta) : maybe_unserialize($meta[0]);
        }
        return $postMeta;
    }

    public function camelToHumanCase($string)
    {
        $string = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $string);
        $string = trim($string);
        return $string;
    }

    public function camelToParamCase($string)
    {
        $string = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', '-$0', $string);
        $string = substr($string, 1);
        return $string;
    }

    public function camelToSnakeCase($string)
    {
        $string = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', '_$0', $string);
        $string = substr($string, 1);
        return $string;
    }
}
