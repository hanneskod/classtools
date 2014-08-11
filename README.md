# classtools [![Build Status](https://travis-ci.org/hanneskod/classtools.svg)](https://travis-ci.org/hanneskod/classtools) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hanneskod/classtools/badges/quality-score.png?s=d9484dda5b07eafdb183746efc126488583e0532)](https://scrutinizer-ci.com/g/hanneskod/classtools/) [![Dependency Status](https://gemnasium.com/hanneskod/classtools.svg)](https://gemnasium.com/hanneskod/classtools)

Find, extract and process classes from file system


## Iterator examples

### Iterate over classes in project

```php
$classes = iterator_to_array(new ClassIterator(PATH_TO_CLASSTOOLS));

// prints path to hanneskod\classtools\Iterator\ClassIterator
echo $classes['hanneskod\classtools\Iterator\ClassIterator'];
```

### Find classes based on type

```php
$it = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints all FilterInterface types (including the interface itself)
print_r(iterator_to_array(
    $it->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
));

// prints instantiable classes that implement FilterInterface
print_r(iterator_to_array(
    $it->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
       ->where('isInstantiable')
));
```

### Find classes based on name

```php
$it = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints classes and interfaces in the Filter namespace
print_r(iterator_to_array(
    $it->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
));
```

### Negate filters

```php
$it = new FilterableClassIterator(
    new ClassIterator(PATH_TO_CLASSTOOLS)
);

// prints all classes and interfaces NOT in the Filter namespace
print_r(iterator_to_array(
    $it->not(
        $it->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
    )
));
```

## Translator examples

### Wrap code in namespace

```php
$reader = new Reader("<?php class Bar {}");

// Outputs class Bar wrapped in namespace Foo
echo $reader->read('Bar')
    ->apply(new Action\NamespaceWrapper('Foo'))
    ->write();
```

### Strip statements

```php
$reader = new Reader("<?php require 'Foo.php'; echo 'bar';");

// Outputs the echo statement
echo $reader->readAll()
    ->apply(new Action\NodeStripper('PhpParser\Node\Expr\Include_'))
    ->write();
```
