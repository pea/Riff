# User

The user class takes care of user creation, updating, and validation. It also includes functionality to handle uploads for things like display pictures.

## Creating a New User

```php
use \Theme\Common\User\CustomerUser;

$user = new CustomerUser;
$user->user_pass = isset($_POST['password']) ? $_POST['password'] : null;
$user->user_email = isset($_POST['email']) ? $_POST['email'] : null;
$user->first_name = isset($_POST['first-name']) ? $_POST['first-name'] : null;
$user->last_name = isset($_POST['last-name']) ? $_POST['last-name'] : null;
$user->save();

if (count($user->errors) > 0) {
    print_r($user->errors);
} else {
    echo 'User saved';
}
```

## Updating a User

Updating a user is exactly the same as creation one but with the addition of their id - $user->ID.

```php
use \Theme\Common\User\CustomerUser;

$user = new CustomerUser;
$user->ID = 1;
$user->user_pass = isset($_POST['password']) ? $_POST['password'] : null;
$user->user_email = isset($_POST['email']) ? $_POST['email'] : null;
$user->first_name = isset($_POST['first-name']) ? $_POST['first-name'] : null;
$user->last_name = isset($_POST['last-name']) ? $_POST['last-name'] : null;
$user->save();

if (count($user->errors) > 0) {
    print_r($user->errors);
} else {
    echo 'User saved';
}
```

## Meta Data

User meta data, or custom user data, must be defined in $this->meta.

```php
public $meta = [
    '<field name>' => [
        'title' => '<field title>',
        'rules' => ['<validation rules>']
    ]
];
```

Saving these fields looks like this.

```php
$user->meta['<field name>']['value'] = isset($_POST['<field_name>']) ? $_POST['<field name>'] : null;
```

## Validation

The default WordPress fields are validated by default, however rules can be added, edited and remove by editing $this->validation. Each field takes an array of validation names. A selection of the most common ones are provided. They are:

- notEmpty
- url
- email
- password
- phone
- alpha
- digit

```php
public $validation = [
    'description' => [],
    'display_name' => [],
    'first_name' => ['notEmpty', 'alpha'],
    'ID' => ['digit'],
    'last_name' => ['notEmpty', 'alpha'],
    'user_email' => ['notEmpty', 'email'],
    'user_login' => [],
    'user_nicename' => [],
    'user_pass' => ['notEmpty', 'password'],
    'user_url' => ['url']
];
```

### Creating Validation Rules

To make a new validation rule simply create a new method which takes $name (human readable input title) and $value and returns an error message on failure (string). The name of this function is what will be used in $this->validation.

```php
public $validation = [
    'description' => ['smallText']
];
```

```php
public function smallText($name, $value)
{
    if (strlen($value) >= 100) {
        return $name . ' should have a length smaller than 100 characters';
    }
}
```

Riff bundles the very comprehensive [respect/validation](https://packagist.org/packages/respect/validation) validation library for you to use.

## Validating Files

A validation rule for a file might look like this.

```php
public function displayPicture($name, $value)
{
    // Mimes
    $mimes = [
        'image/png',
        'image/jpeg'
    ];
    if (!in_array($value['type'], $mimes)) {
        return $name . ' should either be PNG or JPG';
    }
    // Size
    $max = 1;  // MB
    if ($value['size'] > $max * 1000000) {
        return $name . ' should be smaller than ' . $max . 'MB';
    }
}
```

### Notes

- Riff does not set up the user meta fields for you. You should use something like [Carbon Fields](carbonfields.net) to do this.
- WordPress' file upload error messages are currently ignored. This may be resolved in future releases. For now do validation through Riff only. The errors you're missing out on are:
    - The uploaded file was only partially uploaded
    - Missing a temporary folder
    - Failed to write file to disk
    - File upload stopped by extension