<?php
namespace hanneskod\classtools\Iterator;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoConstructArgs()
    {
        foreach (new ClassIterator as $class) {
            $this->assertTrue(false, 'This line should never happen');
        }
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
}
