<?php
namespace hanneskod\classtools\Transformer;

class WriterTest extends \PHPUnit\Framework\TestCase
{
    public function testApplyTranslation()
    {
        $translation = $this->prophesize('PhpParser\NodeVisitor')->reveal();

        $traverser = $this->prophesize('PhpParser\NodeTraverser');
        $traverser->addVisitor($translation)->shouldBeCalled();

        $writer = new Writer($traverser->reveal());
        $writer->apply($translation);
    }

    public function testWrite()
    {
        $writer = new Writer();
        $this->assertEquals('', $writer->write([]));
    }

    public function testPhpParserException()
    {
        $traverser = $this->prophesize('PhpParser\NodeTraverser');
        $traverser->traverse([])->willThrow(new \PhpParser\Error('error'));

        $writer = new Writer($traverser->reveal());

        $this->expectException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write([]);
    }
}
