# Post Type

## Create a Custom Post Type

1. Duplicate Common/PostType/ExamplesPostType.php
2. Rename instances of 'example' to name of new post type
3. Open functions.php and add `$<Name>PostType = new Common\PostType\<Name>PostType;`
4. Open composer.json and add `Common/PostType/<Name>PostType.php` to the files array
5. Run `composer dump-autoload`

## Adding Additional Data to Posts

Riff allows you to append custom data to post objects. You can add data in preparePost().

In single.php pages this data is available in $post. When using Wp_Query the preparePost method must be called to collect it.

## Get Data with Wp_Query

```php
$query = new WP_Query([ 'post_type' => 'example' ]);

foreach ($query->posts as $example) {
     $example = $ExamplesPostType->preparePost($example);
     print_r($example);
}
```