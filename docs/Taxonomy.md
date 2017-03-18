# Taxonomy

## Create a Taxonomy

Extending the Taxonomy class will create a new taxonomy (see [Examples/Taxonomy/ColoursTaxonomy.php](../Examples/Taxonomy/ColoursTaxonomy.php)).

## Settings

### Attach the Taxonomy to Post Types

```php
public $postTypes = ['example', 'post'];
```

### Make the Taxonomy Hierarchical

```php
public $hierarchical = true;
```

### Add Terms to the Taxonomy

```php
public $terms = [
    'red' => 'Red',
    'green' => 'Green',
    'blue' => 'Blue'
];
```