<?php
namespace hanneskod\classtools\Iterator\Filter;

class FilterInterfaceTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterableNotSetException()
    {
        $filter = new TypeFilter('');

        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        $filter->getBoundIterator();
    }
}
