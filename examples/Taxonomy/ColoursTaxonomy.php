<?php

namespace Theme\Common\Taxonomy;

use Riff\Taxonomy\Taxonomy;

use WP_Post;
use WP_Query;

class ColoursTaxonomy extends Taxonomy
{
    /**
     * Hierarchical or non-hierarchical taxonomies
     * @var boolean
     */
    public $hierarchical = true;

    /**
     * Post types to attach taxonomy to
     * @var array
     */
    public $postTypes = ['example', 'post'];

    /**
     * Top-level terms to initially apply. Removing items
     * from here will not remove them from the taxonomy
     * @var array
     */
    public $terms = [
        'red' => 'Red',
        'green' => 'Green',
        'blue' => 'Blue'
    ];
}
