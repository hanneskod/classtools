# classtools

Iterate over classes found in filesystem

## Iterator examples

### Iterate over classes in project

```php
$pathToClasstools = __DIR__.'/../src';
$classIterator = new ClassIterator($pathToClasstools);

$arrayOfClassesInProject = iterator_to_array($classIterator);

// prints path to hanneskod\classtools\ClassIterator
echo $arrayOfClassesInProject['hanneskod\classtools\ClassIterator'];
```

### Find classes based on type

```php
$pathToClasstools = __DIR__.'/../src';
$classIterator = new ClassIterator($pathToClasstools);
$filterableIterator = new FilterableClassIterator($classIterator);

// prints all FilterInterface types (including the interface itself)
print_r(
    iterator_to_array(
        $filterableIterator->filterType('hanneskod\classtools\Filter\FilterInterface')
    )
);

// prints instantiable classes that implement FilterInterface
print_r(
    iterator_to_array(
        $filterableIterator
            ->filterType('hanneskod\classtools\Filter\FilterInterface')
            ->where('isInstantiable')
    )
);
```

### Find classes based on name

```php
$pathToClasstools = __DIR__.'/../src';
$classIterator = new ClassIterator($pathToClasstools);
$filterableIterator = new FilterableClassIterator($classIterator);

// prints all classes in the Filter namespace
print_r(
    iterator_to_array(
        $filterableIterator
            ->filterName('/^hanneskod\\\classtools\\\Filter\\\/')
            ->where('isInstantiable')
    )
);
```

### Negete filters

```php
$pathToClasstools = __DIR__.'/../src';
$classIterator = new ClassIterator($pathToClasstools);
$filterableIterator = new FilterableClassIterator($classIterator);

// prints all classes NOT in the Filter namespace
print_r(
    iterator_to_array(
        $filterableIterator
            ->not(
                $filterableIterator->filterName('/^hanneskod\\\classtools\\\Filter\\\/')
            )
            ->where('isInstantiable')
    )
);
```


Installation using [composer](http://getcomposer.org/)
------------------------------------------------------
To your `composer.json` add

    "require": {
        "hanneskod/classtools": "dev-master@dev",
    }


Testing using [phpunit](http://phpunit.de/)
-------------------------------------------
The unis tests requires that dependencies are installed using composer.

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install --dev
    $ vendor/bin/phpunit
