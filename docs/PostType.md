# Post Type

## Create a Post Type

Extending the PostType class will create a new post type (see [Examples/PostType/ExamplePostType.php](../../Examples/PostType/ExamplePostType.php)). The class name you choose will be used for the post type and should be suffixed with `PostType`.

## Handling Pre-Existing Post Types

Riff can handle post types created by plugins, as well as the Post and Page post types. Simply match the name of your classes with them, e.g. `PagesPostType` or `PostPostType`.

## Populating $post with Additional Data

The preparePost method allows you to modify $post.

```php
public function preparePost(WP_Post $post)
{
    $post = parent::preparePost($post);
    $post->foo = 'bar';
    return $post;
}
```

When retrieving posts with Wp_Query this data will not be available without calling `preparePost`.

```php
$query = new WP_Query([ 'post_type' => 'example' ]);

foreach ($query->posts as $example) {
     $example = $ExamplesPostType->preparePost($example);
     print_r($example);
}
```

### Notes

- Currently only post types with alphanumeric characters are supported.