<p align="center">
    <img src="https://raw.githubusercontent.com/israel-nogueira/blade-x/main/src/topo_README_v3.jpg"/>
</p>

<p align="center">
    <a href="#instalação" target="_Self">Instalação</a> | <a href="#modo-de-uso" target="_Self">Modo de uso</a> 
</p>
<p align="center">
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/v/stable.svg"></a>
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/downloads"></a>
    <a href="https://packagist.org/packages/israel-nogueira/blade-x"><img src="https://poser.pugx.org/israel-nogueira/blade-x/license.svg"></a>
</p>

## Instalação

Faça a instalação via composer:

```bash
composer require israel-nogueira/blade-x
```

## Modo de uso

Crie uma instância do Blade passando a(s) pasta(s) onde seus arquivos de exibição estão localizados e uma pasta de cache. 
Renderize um modelo chamando o método `make`. 
Mais informações sobre o mecanismo de modelagem Blade podem ser encontradas em https://laravel.com/docs/10.x/views.

```php
<?
    include "/vendor/autoload.php";
    use israelNogueira\bladex\BladeX;

	$views = __DIR__ . '/views';
	$cache = __DIR__ . '/cache';
	$bladex = new BladeX($views, $cache);
    
    echo $bladex->make('homepage', ['name' => 'John Doe'])->render();

```

Alternativamente, você pode usar o método abreviado `render`:

```php

    echo $bladex->render('homepage', ['name' => 'John Doe']);

```

Você também pode estender o Blade usando a função `directive()`:

```php

    $bladex->directive('datetime', function ($expression) {
        return "<?php echo with({$expression})->format('F d, Y g:i a'); ?>";
    });

```

O que permite que você use o seguinte em seu modelo de lâmina:

```

Current date: @datetime($date)

```
As instâncias do BladeX passam todos os métodos para a *Factory* de exibição interna.
Assim, métodos como `exists`, `file`, `share`, `composer` e `creator` também estão disponíveis.

Confira a [documentação original](https://laravel.com/docs/10.x/views) para mais informações.

