<?php
namespace hanneskod\classtools\Transformer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyTranslation()
    {
        $translation = $this->createMock('PhpParser\NodeVisitor');

        $traverser = $this->createMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('addVisitor')
            ->with($translation);

        $writer = new Writer($traverser);
        $writer->apply($translation);
    }

    public function testWrite()
    {
        $writer = new Writer();
        $this->assertEquals('', $writer->write([]));
    }

    public function testPhpParserException()
    {
        $traverser = $this->createMock('PhpParser\NodeTraverser');
        $traverser->expects($this->once())
            ->method('traverse')
            ->will($this->throwException(new \PhpParser\Error('error')));

        $writer = new Writer($traverser);

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write([]);
    }
}
