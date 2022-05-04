<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Iterator\Filter;

use hanneskod\classtools\Iterator\ClassIterator;
use hanneskod\classtools\Iterator\Filter;

/**
 * Filter classes based on presence of a PHP 8 attribute on a class level
 * Note this is a basic implementation that simply looks for at least one declaration
 * of a given attribute class and considers this a match
 *
 * Attribute class name must be passed, not an instance of the attribute
 *
 * #[Attribute]
 * class CoolStuff { ... }
 * 
 * #[CoolStuff]
 * class BeCool { ... }
 *
 * // Print all classes tagged with a CoolStuff attribute
 * foreach ($iter->attribute(CoolStuff::class) as $class) {
 *   echo $class->getName();
 * }
 *
 * Note this works only in PHP 8.0.1 or higher, but syntax is PHP 7 compaptible
 * so whilst this cannot be used on earlier versions it also won't break them
 *
 * @author Mark Hewitt <mark.hewitt@centurionsolutions.com.au>
 */
final class AttributeFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * @var string 
     */
    private $attribute_class_name;

    /**
     * Register matching attribute name
     */
    public function __construct(string $attribute_class_name)
    {
        parent::__construct();
        $this->attribute_class_name = $attribute_class_name;
    }

    public function getIterator(): iterable
    {
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
			// if the class implements the given attribute (one or more times)
			// then this is a match, and we yield with the found class
            if ( !empty($reflectedClass->getAttributes($this->attribute_class_name, \ReflectionAttribute::IS_INSTANCEOF)) ) {
                yield $className => $reflectedClass;
            }
        }
    }
}
