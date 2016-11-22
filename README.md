## Riff
PHP library written for Wordpress to take care of common tasks such as creating custom post types and taxonomies.

- [Riff](#riff)
  * [Suggested File Structure](#suggested-file-structure)
  * [Create a Custom Post Type](#create-a-custom-post-type)
  * [Adding Additional Data to Posts](#adding-additional-data-to-posts)
  * [Get Data with Wp_Query](#get-data-with-wp-query)
  * [Create a Taxonomy](#create-a-taxonomy)
  * [Vendors](#vendors)
    + [Adding PHP vendors](#adding-php-vendors)
  * [Custom User Registration and Login Forms](#custom-user-registration-and-login-forms)
    + [Redirect User After Registration](#redirect-user-after-registration)
    + [Redirect User After Failed Login](#redirect-user-after-failed-login)
    + [Redirect User After Login](#redirect-user-after-login)
  * [API](#api)
  * [AWS S3 Media Uploads](#aws-s3-media-uploads)

### Create a Custom Post Type

1. Duplicate Common/PostType/ExamplesPostType.php
2. Rename instances of 'example' to name of new post type
3. Open functions.php and add `$<Name>PostType = new Common\PostType\<Name>PostType;`
4. Open composer.json and add `Common/PostType/<Name>PostType.php` to the files array
5. Run `composer dump-autoload`

### Adding Additional Data to Posts

Riff allows you to append custom data to post objects. You can add data in preparePost().

In single.php pages this data is available in $post. When using Wp_Query the preparePost method must be called to collect it. 

### Get Data with Wp_Query

```php
$query = new WP_Query([ 'post_type' => 'example' ]);

foreach ($query->posts as $example) {
     $example = $ExamplesPostType->preparePost($example);
     print_r($example);
}
```

### Create a Taxonomy

1. Duplicate Common/Taxonomy/ColoursTaxonomy.php
2. Rename instances of 'colour' to name of taxonomy
3. Open functions.php and add `$<Name>Taxonomy = new Common\Taxonomy\<Name>Taxonomy;`
4. Open composer.json and add `Common/Taxonomy/<Name>Taxonomy.php` to the files array
5. Run `composer dump-autoload`

See comments in ColoursTaxonomy.php for options.

### Vendors

#### Adding PHP vendors
PHP vendors should be installed with Composer. For example:
```shell
composer require facebook/graph-sdk
```
And imported with Use. For example:
```php
use \Facebook\Facebook;
```
Or
```php
$fb = new \Facebook\Facebook([
  'app_id' => '{app-id}',
  'app_secret' => '{app-secret}',
  'default_graph_version' => 'v2.7'
]);
```

### Custom User Registration and Login Forms
Functionality is available to insert login and registration forms into the theme. Insert the templates 'component-login.php' and 'component-register' to use.

To redirect users to custom pages after registration or login use one of the below.

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
```

### API
An API for simple tasks is available. To make an endpoint, create the file `Common/Api/MyTask.php`. It will then be available at `http://example.com/api/MyTask`. The contents of this file might look like this:
```php
<?php

namespace Theme\Common;

use Riff\Api\Api;

class UserLogout extends Api
{
    public function init()
    {
        wp_logout();
        echo '[]';
    }
}

new UserLogout;
```
Called with:
```
http://example.com/api/UserLogout
```

Note: If a more advanced API (with authentication) is required, use [WP Rest API](http://v2.wp-api.org/extending/adding/).

### AWS S3 Media Uploads
The S3 class instructs Wordpress to upload media straight to an AWS S3 bucket. To use:

1. create an S3 bucket
2. Attach a policy to the bucket. Example:

        {
            "Version": "2012-10-17",
            "Statement": [
                {
                    "Effect": "Allow",
                    "Principal": "*",
                    "Action": [
                        "s3:GetObject"
                    ],
                    "Resource": [
                        "arn:aws:s3:::YOUR_BUCKET_NAME/*"
                    ]
                }
            ]
        }

3. Create an IAM-user
4. Attach a user policy to the IAM-user. Example:

        {
          "Version": "2012-10-17",
          "Statement": [
            {
              "Sid": "Stmt1407599749000",
              "Effect": "Allow",
              "Action": [
                "s3:DeleteObject",
                "s3:GetObject",
                "s3:GetObjectAcl",
                "s3:PutObject",
                "s3:PutObjectAcl"
              ],
              "Resource": [
                "arn:aws:s3:::YOUR_BUCKET_NAME/*"
              ]
            },
            {
              "Sid": "Stmt1407599782000",
              "Effect": "Allow",
              "Action": [
                "s3:ListBucket"
              ],
              "Resource": [
                "arn:aws:s3:::YOUR_BUCKET_NAME"
              ]
            }
          ]
        }

5. Create an access-key for the user and keep the credentials safe
6. Configure `Common/Config/S3.php` with credentials generated.

Things to note:
- Only new media is uploaded. Old media will need to be manually uploaded and the urls updated in the database
- The url path structure is identical to that used by Wordpress. The only difference is in the domain. For example: https://my-bucket.s3.amazonaws.com/2016/11/beans.png

Class based off [helmutschneider's plugin](https://github.com/helmutschneider/wp-s3)