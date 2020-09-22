# Laravel Tags

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robotsinside/laravel-tags.svg?style=flat-square)](https://packagist.org/packages/robotsinside/laravel-tags)
[![Build Status](https://img.shields.io/travis/robotsinside/laravel-tags/master.svg?style=flat-square)](https://travis-ci.org/robotsinside/laravel-tags)
[![Quality Score](https://img.shields.io/scrutinizer/g/robotsinside/laravel-tags.svg?style=flat-square)](https://scrutinizer-ci.com/g/robotsinside/laravel-tags)
[![Total Downloads](https://img.shields.io/packagist/dt/robotsinside/laravel-tags.svg?style=flat-square)](https://packagist.org/packages/robotsinside/laravel-tags)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

A simple package for tagging Eloquent models in Laravel. This package is a sibling of [Laravel Categories](https://github.com/robotsinside/laravel-categories), which can be used to categorise Eloquent models. The API is the same as this one.

## Installation

1. Install using Composer

```sh
composer require robotsinside/laravel-tags
```

2. Register the service provider in `config/app.php`

```php
/*
* Package Service Providers...
*/
\RobotsInside\Tags\TagsServiceProvider::class,
```

Auto-discovery is enabled, so this step can be skipped.

3. Publish the migrations

```sh
php artisan vendor:publish --provider="RobotsInside\Tags\TagsServiceProvider" --tag="migrations"
```

4. Migrate the database. This will create two new tables; `tags` and `taggables`

```sh
php artisan migrate
```

## Usage

Use the `RobotsInside\Tags\Taggable` trait in your models.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use RobotsInside\Tags\Taggable;

class Post extends Model
{
    use Taggable;
}
```

You are now ready to start tagging. Models can be tagged by passing an integer, array of integers, a model instance or a collection of models.

```php
<?php

use App\Post;
use Illuminate\Support\Facades\Route;
use RobotsInside\Tags\Models\Tag;

Route::get('/', function () {

    // Retrieve a new or existing tag
    $tag1 = (new Tag())->resolve('Tag 1');
    $tag2 = (new Tag())->resolve('Tag 2');

    // Or, retrieve a collection of new or existing tags
    $tags = (new Tag())->resolveAll(['Tag 1', 'Tag 2', 'Tag 3'])

    $post = new Post();
    $post->title = 'My blog';
    $post->save();

    $post->tag($tag1);
    // Or
    $post->tag(['tag-1']);
    // Or
    $post->tag([1, 2]);
    // Or
    $post->tag(Tag::get());
});
```

Untagging models is just as simple.

```php
<?php

use App\Post;
use Illuminate\Support\Facades\Route;
use RobotsInside\Tags\Models\Tag;

Route::get('/', function () {

    $tag1 = Tag::find(1);

    $post = Post::where('title', 'My blog')->first();

    $post->untag($tag1);
    // Or
    $post->untag(['tag-1']);
    // Or
    $post->untag([1, 2]);
    // Or
    $post->untag(Tag::get());
    // Or
    $post->untag(); // remove all tags
});
```

## Scopes

Each time a `RobotsInside\Tags\Models\Tag` is used, the `count` column in the `tags` table is incremented. When a tag is removed, the count is decremented until it is zero.

This packages comes with a number of pre-defined scopes to make queries against the `count` column easier, namely `>=`, `>`, `<=` and `<` contstrains, for example:

-   `Tag::usedGte(1);`
-   `Tag::usedGt(2);`
-   `Tag::usedLte(3);`
-   `Tag::usedLt(4);`

In addition, a scope on the `Taggable` model is provided to constrain records created within the given time frame. This scope supports human readable values including `days`, `months` and `years` in both singular and plural formats, for example:

-   `Taggable::taggedWithin('7 days');`
-   `Taggable::taggedWithin('1 month');`
-   `Taggable::taggedWithin('2 years');`

## Credits

- [Rob Francken](https://github.com/robotsinside)
- [All Contributors](../../contributors)

## Coffee Time

I have a problem. My coffee bean supply is running low. The thought of running out completely is making me sweat bullets. If you don't mind, I'd appreciate your support.

<a href="https://www.buymeacoffee.com/robfrancken" target="_blank" width="50"><img src="https://cdn.buymeacoffee.com/buttons/v2/arial-yellow.png" width="200" alt="Buy Me A Coffee"></a>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
