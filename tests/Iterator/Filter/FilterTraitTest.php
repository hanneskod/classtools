<?php
namespace hanneskod\classtools\Iterator\Filter;

class FilterTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterNotBoundException()
    {
        $filter = new TypeFilter('');
        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        $filter->getBoundIterator();
    }
}
