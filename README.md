<p align="center">
    <img src="https://raw.githubusercontent.com/israel-nogueira/blade-x/main/src/topo_README_v3.jpg"/>
</p>
<p align="center">
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/v/stable.svg"></a>
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/downloads"></a>
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/license.svg"></a>
</p>

## Installation

Install using composer:

```bash
composer require israel-nogueira/blade-x
```

## Usage

Create a Blade instance by passing it the folder(s) where your view files are located, and a cache folder. Render a template by calling the `make` method. More information about the Blade templating engine can be found on https://laravel.com/docs/master/views.

```php
use Jenssegers\Blade\Blade;

$blade = new Blade('views', 'cache');

echo $blade->make('homepage', ['name' => 'John Doe'])->render();
```

Alternatively you can use the shorthand method `render`:

```php
echo $blade->render('homepage', ['name' => 'John Doe']);
```

You can also extend Blade using the `directive()` function:

```php
$blade->directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('F d, Y g:i a'); ?>";
});
```

Which allows you to use the following in your blade template:

```
Current date: @datetime($date)
```

The Blade instances passes all methods to the internal view factory. So methods such as `exists`, `file`, `share`, `composer` and `creator` are available as well. Check out the [original documentation](https://laravel.com/docs/5.8/views) for more information.

## Integrations

- [Phalcon Slayer Framework](https://github.com/phalconslayer/slayer) comes out of the box with Blade.