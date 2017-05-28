# [SyDES](http://sydes.ru) - Lightweight CMF for a simple sites with SQLite database

# WIP

    This is early-dev version of 3.x branch
    If you wanna production version, please check main repo https://github.com/artygrand/SyDES

## Overview

SyDES is a free open source content management framework for small/medium sites with non-typical functionality and complex
design. With iblocks you can put any dynamic or static content to any page that allows you to create any entity, such as a
blog, catalog or even gallery. Multi-Site will allow to create network of satellites with any domain or language.

## Main features

1. Fast and lightweight engine (only ???KB for engine + 3.55MB by ckeditor and ckfinder)
2. Support multi-language for site and admin center
3. Friendly and customizable interface with some magic and changeable skins
4. Easily expandable with modules, plugins, constructors and infoblocks
5. Uses a SQLite3 via PDO
6. Easy the development and integration of new templates, modules and plugins
7. User-friendly URLs, automatic robots.txt, sitemap.xml and rss

## Install

### Old-style

1. Download compiled archive from [last release](https://github.com/sydes/sydes/releases) and unzip in site root folder
2. Open this site in browser
3. Select your language from available
4. Enter login, password and e-mail
5. Create new site

### From console

1. `composer create-project --prefer-dist -sdev sydes/sydes`
2. `php sydes install`

## System requirements

Apache 2.2, PHP 5.4, PDO with sqlite driver, mod_rewrite, JSON, url_fopen or cURL, ZipArchive.

Upload [check.php](check.php) to the server to check the requirements

## Third-party software

+ CKEditor + plugins
+ CKFinder (DEMO)
+ jQuery
+ jQuery UI
+ jquery nestedSortable
+ Bootstrap
+ Bootstrap datePicker

## License

The CMF SyDES is open-sourced software licensed under the [GPL v3](LICENSE) or any later version.
