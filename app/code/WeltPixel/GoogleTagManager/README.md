# m2-weltpixel-google-tag-manager

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.m2-weltpixel-google-tag-manager git git@github.com:rusdragos/m2-weltpixel-google-tag-manager.git
$ composer require weltpixel/module-google-tag-manager:dev-master
```

Manually:

Copy the zip into app/code/WeltPixel/GoogleTagManager directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_GoogleTagManager --clear-static-content
$ php bin/magento setup:upgrade
```
