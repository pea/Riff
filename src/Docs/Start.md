# Start

## Autoloading

Instead of editing the composer.json file to include files in Common/ you can also autoload them by adding this to the `autoload` object in composer.json.

```
"psr-4": {
    "Theme\\": ""
}
```

## Useful Code Snippets

#### Redirect User After Registration
```php
add_filter('registration_redirect', function () {
    return home_url('/registration-confirmation');
});
```

#### Redirect User After Failed Login
```php
add_action('wp_login_failed', function () {
    $referrer = $_SERVER['HTTP_REFERER'];
    if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
        wp_redirect(home_url() . '/?login=failed');
        exit;
    }
});
```

#### Redirect User After Login
```php
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return $redirect_to;
        } else {
            return home_url() . '/custom-page';
        }
    } else {
        return $redirect_to;
    }
}, 10, 3);

### Get Data with Wp_Query

```php
$query = new WP_Query([ 'post_type' => 'example' ]);

foreach ($query->posts as $example) {
     $example = $ExamplesPostType->preparePost($example);
     print_r($example);
}
```