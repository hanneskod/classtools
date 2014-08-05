<?php
namespace hanneskod\classtools;

class InstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionWhenReflectionClassNotSet()
    {
        $in = new Instantiator;
        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        $in->getReflectionClass();
    }

    public function testIsInstantiable()
    {
        $in = new Instantiator;
        $in->setReflectionClass(new \ReflectionClass('hanneskod\classtools\Iterator\FilterableClassIterator'));
        $this->assertTrue($in->isInstantiable());
        $this->assertFalse($in->isInstantiableWithoutArgs());
    }

    public function testExceptionWhenInstantiatingNotInstatiable()
    {
        $in = new Instantiator;
        $in->setReflectionClass(new \ReflectionClass('hanneskod\classtools\Exception'));
        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        $in->instantiate();
    }

    public function testExceptionWhenInstantiatingWithToFewArgs()
    {
        $in = new Instantiator;
        $in->setReflectionClass(new \ReflectionClass('hanneskod\classtools\Iterator\FilterableClassIterator'));
        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        $in->instantiate();
    }

    public function testInstantiate()
    {
        $in = new Instantiator;
        $in->setReflectionClass(new \ReflectionClass('hanneskod\classtools\Instantiator'));
        $this->assertInstanceOf('hanneskod\classtools\Instantiator', $in->instantiate());
    }
}
