# classtools [![Build Status](https://travis-ci.org/hanneskod/classtools.svg)](https://travis-ci.org/hanneskod/classtools) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hanneskod/classtools/badges/quality-score.png?s=d9484dda5b07eafdb183746efc126488583e0532)](https://scrutinizer-ci.com/g/hanneskod/classtools/)

Iterate over classes found in filesystem

## Iterator examples

### Iterate over classes in project

```php
$classes = iterator_to_array(new ClassIterator(PATH_TO_CLASSTOOLS));

// prints path to hanneskod\classtools\Iterator\ClassIterator
echo $classes['hanneskod\classtools\Iterator\ClassIterator'];
```

### Find classes based on type

```php
$filterableIterator = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints all FilterInterface types (including the interface itself)
print_r(
    iterator_to_array(
        $filterableIterator->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
    )
);

// prints instantiable classes that implement FilterInterface
print_r(
    iterator_to_array(
        $filterableIterator
            ->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
            ->where('isInstantiable')
    )
);
```

### Find classes based on name

```php
$filterableIterator = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints classes and interfaces in the Filter namespace
print_r(
    iterator_to_array(
        $filterableIterator
            ->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
    )
);
```

### Negate filters

```php
$filterableIterator = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints all classes and interfaces NOT in the Filter namespace
print_r(
    iterator_to_array(
        $filterableIterator
            ->not(
                $filterableIterator->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
            )
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
