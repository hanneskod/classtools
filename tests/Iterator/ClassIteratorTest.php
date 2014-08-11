<?php
namespace hanneskod\classtools\Iterator;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoConstructArgs()
    {
        $this->assertEmpty(
            iterator_to_array(new ClassIterator),
            'No arguments to constructor should yield no found classes'
        );
    }

    public function testInvalidConstructorArgs()
    {
        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        new ClassIterator(array('not-a-file-or-dir'));
    }

    public function testScanFile()
    {
        $this->assertArrayHasKey(
            __CLASS__,
            iterator_to_array(new ClassIterator(__FILE__))
        );
    }

    public function testScanDir()
    {
        $this->assertArrayHasKey(
            __CLASS__,
            iterator_to_array(new ClassIterator(array(__DIR__)))
        );
    }

    public function testGetClassmap()
    {
        $iter = new ClassIterator(__FILE__);
        $this->assertArrayHasKey(
            __CLASS__,
            iterator_to_array($iter->getClassMap())
        );
    }
}
