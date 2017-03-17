# Taxonomy

## Create a Taxonomy

1. Duplicate Common/Taxonomy/ColoursTaxonomy.php
2. Rename instances of 'colour' to name of taxonomy
3. Open functions.php and add `$<Name>Taxonomy = new Common\Taxonomy\<Name>Taxonomy;`
4. Open composer.json and add `Common/Taxonomy/<Name>Taxonomy.php` to the files array
5. Run `composer dump-autoload`

See comments in ColoursTaxonomy.php for options.