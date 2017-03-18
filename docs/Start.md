# Start

## Install

Initiate a new Composer project into a WordPress theme and install Riff.

`composer require pea/riff`

## Autoloading Your Classes

Add this to your composer.json.

```json
"autoload": {
  "psr-4": {
    "Theme\\": ""
  }
}
```

Add Composer's autoloader to your functions.php

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Initiating your Riff Classes

After including the Composer autoloader you can initiate your Riff classes.

```php
namespace Theme;

require_once __DIR__ . '/vendor/autoload.php';

// Custom Post Types
new Common\PostType\PostsPostType;
new Common\PostType\PagesPostType;
new Common\PostType\EventsPostType;

// Taxonomies
new Common\Taxonomy\ColoursTaxonomy;

// Api
new Common\Api\userLogout;
new Common\Api\userRegistration;

// Users
new Common\User\CustomerUser;

// S3
new \Riff\S3\S3(require('Common/Config/S3.php'));
```