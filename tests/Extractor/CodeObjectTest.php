<?php
namespace hanneskod\classtools\Extractor;

class CodeObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetPrinter()
    {
        $co = new CodeObject(array());

        $this->assertInstanceOf(
            'hanneskod\classtools\Extractor\BracketingPrinter',
            $co->getPrinter()
        );

        $printer = $this->getMock('PhpParser\PrettyPrinterAbstract');
        $co->setPrinter($printer);
        $this->assertSame($printer, $co->getPrinter());
    }

    public function testRegisterVisitor()
    {
        $visitor = $this->getMock('PhpParser\NodeVisitor');

        $traverser = $this->getMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('addVisitor')
            ->with($visitor);

        $co = new CodeObject(array(), $traverser);
        $co->registerVisitor($visitor);
    }

    public function testGetCode()
    {
        $co = new CodeObject(array());
        $this->assertEquals('', $co->getCode());
    }

    public function testPhpParserException()
    {
        $traverser = $this->getMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('traverse')
            ->will($this->throwException(new \PhpParser\Error('error')));

        $co = new CodeObject(array(), $traverser);

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $co->getCode();
    }
}
