<?php

declare(strict_types = 1);

namespace hanneskod\classtools\Iterator\Filter;

class FilterTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testFilterNotBoundException()
    {
        $filter = new TypeFilter('');
        $this->expectException('hanneskod\classtools\Exception\LogicException');
        $filter->getBoundIterator();
    }
}
