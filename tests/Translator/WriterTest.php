<?php
namespace hanneskod\classtools\Translator;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetPrinter()
    {
        $writer = new Writer([]);

        $this->assertInstanceOf(
            'hanneskod\classtools\Translator\BracketingPrinter',
            $writer->getPrinter()
        );

        $printer = $this->getMock('PhpParser\PrettyPrinterAbstract');
        $writer->setPrinter($printer);
        $this->assertSame($printer, $writer->getPrinter());
    }

    public function testApplyTranslation()
    {
        $translation = $this->getMock('PhpParser\NodeVisitor');

        $traverser = $this->getMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('addVisitor')
            ->with($translation);

        $writer = new Writer([], $traverser);
        $writer->apply($translation);
    }

    public function testWrite()
    {
        $writer = new Writer([]);
        $this->assertEquals('', $writer->write());
    }

    public function testPhpParserException()
    {
        $traverser = $this->getMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('traverse')
            ->will($this->throwException(new \PhpParser\Error('error')));

        $writer = new Writer([], $traverser);

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write();
    }
}
