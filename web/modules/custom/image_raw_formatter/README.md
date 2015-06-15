Image Raw Field Formatter
=====================

Image Raw Formatter for Drupal 8. This formatter return URLs absolute of original images or image style if is configured. The default formates form image doens't work for REST services because return HTML tags for images.

### Install
```bash
$ cd path/to/drupal/8/modules
$ git clone https://github.com/enzolutions/image_raw_formatter.git
$ drush en -y image_raw_formatter # or enable this module via UI
```

### Usage

 * In your content type create a new Image field
 * Go to /admin/structure/types/manage/[Content-Type]/display
 * Change the format field to use Image Raw formatter

Resources
---------

You can run the unit tests with the following command:

    $ composer install
    $ vendor/bin/phpunit
