<?php
namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;

class NamespaceWrapperTest extends \PHPUnit\Framework\TestCase
{
    public function testWrapCodeInNamespace()
    {
        $readerOne = new Reader(
<<<EOF
<?php
class ClassName
{
}
EOF
        );

        $readerTwo = new Reader(
<<<EOF
<?php
namespace {
    class ClassName
    {
    }
}
EOF
        );

        $expected =
<<<EOF
namespace NamespaceName {
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer;
        $writer->apply(new NamespaceWrapper('NamespaceName'));
        $this->assertEquals(
            $expected,
            $writer->write($readerOne->read('ClassName'))
        );
        $this->assertEquals(
            $expected,
            $writer->write($readerTwo->read('ClassName'))
        );
    }

    public function testExtendNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace NamespaceName {
    class ClassName
    {
    }
}
EOF
        );

        $expected =
<<<EOF
namespace extended\NamespaceName {
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer;
        $writer->apply(new NamespaceWrapper('extended'));
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('NamespaceName\ClassName'))
        );
    }

    public function testIgnoreExtendedEmptyNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace foobar {
    class ClassName
    {
    }
}
EOF
        );

        $expected =
<<<EOF
namespace foobar {
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer;
        // Assert that a empty second wrapper makes no difference
        $writer->apply(new NamespaceWrapper(''));
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('foobar\ClassName'))
        );
    }

    public function testCreateNewNamespaceNode()
    {
        $wrapper = new NamespaceWrapper('foobar');
        $this->assertInstanceOf(
            'PhpParser\Node\Stmt\Namespace_',
            $wrapper->beforeTraverse([])[0]
        );
    }
}
