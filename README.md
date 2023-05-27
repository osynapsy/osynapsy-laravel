# What is Osynapsy Laravel? #
OsynapsyLaravel is a library for build html tag and components in Php.

## Installation ##
It's recommended that you use [Composer](https://getcomposer.org/) to install osynapsy-laravel.

```bash
$ composer require osynapsy.net/osynapsy-laravel "@stable"
```

## Usage
```php

<?php

$div = new \Osynapsy\Html\Tag('div', 'div1', 'card');
$div->addClass('bg-white')->add('Test');

echo $div;

```

result

```text
<div id="div1" class="card bg-white">Test</div>
```