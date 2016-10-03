<?php

namespace Riff\Taxonomy;

use Inflect\Inflect;

use WP_POST;
use WP_Query;

class Taxonomy
{
    public $hierarchical = true;
    public $postTypes = ['post'];
    public $terms = [];

    public function __construct()
    {
        $taxonomyName = basename(get_class($this), 'Taxonomy');
        $taxonomyName = explode('\\', $taxonomyName);
        $this->taxonomyName = end($taxonomyName);

        $this->init();
    }

    public function themeActivation()
    {
        $this->init();
        flush_rewrite_rules();
    }

    public function init()
    {
        $options = [
            'labels' => [
                'name' => __(Inflect::singularize($this->taxonomyName)),
                'singular_name' => __(Inflect::singularize($this->taxonomyName)),
                'all_items' => __('All ' . Inflect::pluralize($this->taxonomyName)),
                'edit_item' => __('Edit ' . Inflect::singularize($this->taxonomyName)),
                'view_item' => __('View ' . Inflect::singularize($this->taxonomyName)),
                'update_item' => __('Update ' . Inflect::singularize($this->taxonomyName)),
                'add_new_item' => __('Add New ' . Inflect::singularize($this->taxonomyName)),
                'new_item_name' => __('New ' . Inflect::singularize($this->taxonomyName) . ' Name'),
                'parent_item' => __('Parent ' . Inflect::singularize($this->taxonomyName)),
                'parent_item_colon' => __('Parent ' . Inflect::singularize($this->taxonomyName) . ':'),
                'search_items' => __('Search ' . Inflect::pluralize($this->taxonomyName)),
                'popular_items' => __('Popular' . Inflect::pluralize($this->taxonomyName)),
                'separate_items_with_commas' => __('Separate ' . Inflect::pluralize($this->taxonomyName) . ' with commas'),
                'add_or_remove_items' => __('Add or remove ' . Inflect::pluralize($this->taxonomyName)),
                'choose_from_most_used' => __('Choose from the most used ' . Inflect::pluralize($this->taxonomyName)),
                'not_found' => __('No ' . Inflect::pluralize($this->taxonomyName) . ' found')
            ],
            'hierarchical' => $this->hierarchical,
            'query_var' => true,
            'rewrite' => [
                'slug' => strtolower(Inflect::pluralize($this->taxonomyName))
            ]
        ];
        register_taxonomy(
            strtolower(Inflect::singularize($this->taxonomyName)),
            $this->postTypes,
            wp_parse_args($options, $options)
        );
        $this->addTerms();
    }

    public function addTerms()
    {
        foreach ($this->terms as $key => $term) {
            if (!term_exists($key, strtolower(Inflect::pluralize($this->taxonomyName)))) {
                wp_insert_term(
                    $term,
                    strtolower(Inflect::singularize($this->taxonomyName)),
                    [
                        'slug' => $key
                    ]
                );
            }
        }
    }
}
