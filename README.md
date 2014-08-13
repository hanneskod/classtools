# classtools [![Build Status](https://travis-ci.org/hanneskod/classtools.svg)](https://travis-ci.org/hanneskod/classtools) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hanneskod/classtools/badges/quality-score.png?s=d9484dda5b07eafdb183746efc126488583e0532)](https://scrutinizer-ci.com/g/hanneskod/classtools/) [![Dependency Status](https://gemnasium.com/hanneskod/classtools.svg)](https://gemnasium.com/hanneskod/classtools)

Find, extract and process classes from file system

## Iterator

[ClassIterator](src/Iterator/ClassIterator.php) consumes a [symfony
finder](http://symfony.com/doc/current/components/finder.html) and scans files
for php classes, interfaces and traits.

### Access the class map

`getClassMap()` returns a map of class names to
[SplFileInfo](http://api.symfony.com/2.5/Symfony/Component/Finder/SplFileInfo.html)
objects.

```php
$finder = new Finder;
$iter = new ClassIterator($finder->in('src'));

// Print the file names of classes, interfaces and traits in 'src'
foreach ($iter->getClassMap() as $name => $splFileInfo) {
    echo $splFileInfo->getRealPath();
}
```

### Iterate over ReflectionClass objects

ClassIterator is also a
[Traversable](http://php.net/manual/en/class.traversable.php), that on iteration
yields class names as keys and
[ReflectionClass](http://php.net/manual/en/class.reflectionclass.php) objects as
values.

```php
$finder = new Finder();
$iter = new ClassIterator($finder->in('src'));

// Prints all classes, interfaces and traits in 'src'
foreach ($iter as $name => $reflectionClass) {
    echo $name;
}
```

### Filter based on class properties

[ClassIterator](src/Iterator/ClassIterator.php) is filterable and filters are
chainable.

```php
$finder = new Finder();
$iter = new ClassIterator($finder->in('src'));

// Prints all Filter types (including the interface itself)
foreach ($iter->type('Iterator\Filter') as $name => $reflectionClass) {
    echo $name;
}

// Prints classes, interfaces and traits in the Iterator namespace
foreach ($iter->name('/Iterator\\\/') as $name => $reflectionClass) {
    echo $name;
}

// Prints implementations of the Filter interface
$iter = $iter->type('Iterator\Filter')->where('isInstantiable');
foreach ($iter as $name => $reflectionClass) {
    echo $name;
}
```

### Negate filters

Filters can also be negated by wrapping them in `not()` method calls.

```php
$finder = new Finder();
$iter = new ClassIterator($finder->in('src'));

// Prints all classes, interfaces and traits NOT instantiable
foreach ($iter->not($iter->where('isInstantiable')) as $name => $reflectionClass) {
    echo $name;
}
```

### Transforming classes

Found class, interface and trait definitions can be transformed and written to a
single file.

```php
$finder = new Finder();
$iter = new ClassIterator($finder->in('src'));

// Prints all found definitions in one snippet
echo $iter->minimize();

// The same can be done using
echo $iter->transform(new MinimizingWriter);
```

## Translator examples

### Wrap code in namespace

```php
$reader = new Reader("<?php class Bar {}");
$writer = new Writer;
$writer->apply(new Action\NamespaceWrapper('Foo'));

// Outputs class Bar wrapped in namespace Foo
echo $writer->write($reader->read('Bar'));
```

### Strip statements

```php
$reader = new Reader("<?php require 'Foo.php'; echo 'bar';");
$writer = new Writer;
$writer->apply(new Action\NodeStripper('PhpParser\Node\Expr\Include_'));

// Outputs the echo statement
echo $writer->write($reader->readAll());
```
