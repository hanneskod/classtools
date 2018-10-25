# hanneskod/classtools

[![Packagist Version](https://img.shields.io/packagist/v/hanneskod/classtools.svg?style=flat-square)](https://packagist.org/packages/hanneskod/classtools)
[![Build Status](https://img.shields.io/travis/hanneskod/classtools/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/classtools)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/classtools.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/classtools)

Find, extract and process classes from the file system.

Installation
------------
Install using **[composer](http://getcomposer.org/)**. Exists as
**[hanneskod/classtools](https://packagist.org/packages/hanneskod/classtools)**
in the **[packagist](https://packagist.org/)** repository. From the command line
use:

    composer require hanneskod/classtools:~1.0

Using the iterator
------------------
[ClassIterator](src/Iterator/ClassIterator.php) consumes a [symfony
finder](http://symfony.com/doc/current/components/finder.html) and scans files
for php classes, interfaces and traits.

### Access the class map

`getClassMap()` returns a map of class names to
[SplFileInfo](http://api.symfony.com/2.5/Symfony/Component/Finder/SplFileInfo.html)
objects.

<!--
    @example getClassMap()
    @expectOutput "/hanneskod/"
-->
```php
$finder = new Symfony\Component\Finder\Finder;
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));

// Print the file names of classes, interfaces and traits in 'src'
foreach ($iter->getClassMap() as $classname => $splFileInfo) {
    echo $classname.': '.$splFileInfo->getRealPath();
}
```

### Find syntax errors

Source files containing syntax errors can not be parsed and hence no information
on contained classes can be retrieved. Use `getErrors()` to read the list of
encountered errors.

<!--
    @example getErrors()
    @expectOutput "/Array/"
-->
```php
$finder = new Symfony\Component\Finder\Finder;
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));

print_r($iter->getErrors());
```

### Iterate over ReflectionClass objects

ClassIterator is also a
[Traversable](http://php.net/manual/en/class.traversable.php), that on iteration
yields class names as keys and
[ReflectionClass](http://php.net/manual/en/class.reflectionclass.php) objects as
values.

Note that to use reflection the classes found in filesystem must be
included in the environment. Enable autoloading to dynamically load classes from
a ClassIterator.

<!--
    @example enableAutoloading()
    @expectOutput "/hanneskod/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));

// Enable reflection by autoloading found classes
$iter->enableAutoloading();

// Print all classes, interfaces and traits in 'src'
foreach ($iter as $class) {
    echo $class->getName();
}
```

### Filter based on class properties

[ClassIterator](src/Iterator/ClassIterator.php) is filterable and filters are
chainable.

<!--
    @example filter
    @expectOutput "/hanneskod/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all Filter types (including the interface itself)
foreach ($iter->type('hanneskod\classtools\Iterator\Filter') as $class) {
    echo $class->getName();
}

// Print definitions in the Iterator namespace whose name contains 'Class'
foreach ($iter->inNamespace('hanneskod\classtools\Iterator\Filter')->name('/type/i') as $class) {
    echo $class->getName();
}

// Print implementations of the Filter interface
foreach ($iter->type('hanneskod\classtools\Iterator\Filter')->where('isInstantiable') as $class) {
    echo $class->getName();
}
```

### Negate filters

Filters can also be negated by wrapping them in `not()` method calls.

<!--
    @example negation
    @expectOutput "/hanneskod/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all classes, interfaces and traits NOT instantiable
foreach ($iter->not($iter->where('isInstantiable')) as $class) {
    echo $class->getName();
}
```

### Transforming classes

Found class, interface and trait definitions can be transformed and written to a
single file.

<!--
    @example transformation
    @expectOutput "/\<\?php/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new hanneskod\classtools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all found definitions in one snippet
echo $iter->minimize();

// The same can be done using
echo $iter->transform(new hanneskod\classtools\Transformer\MinimizingWriter);
```

Using the transformer
---------------------

### Wrap code in namespace

<!-- @ignore -->
```php
$reader = new Reader("<?php class Bar {}");
$writer = new Writer;
$writer->apply(new Action\NamespaceWrapper('Foo'));

// Outputs class Bar wrapped in namespace Foo
echo $writer->write($reader->read('Bar'));
```
