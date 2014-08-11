<?php
namespace hanneskod\classtools\Translator\Action;

use hanneskod\classtools\Translator\Reader;

class NamespaceWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testWrapCodeInNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName
{
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

        $writer = $reader->read('ClassName');
        $writer->apply(new NamespaceWrapper('NamespaceName'));
        $this->assertEquals(
            $expected,
            $writer->write()
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

        $writer = $reader->read('NamespaceName\ClassName');
        $writer->apply(new NamespaceWrapper('extended'));
        $this->assertEquals(
            $expected,
            $writer->write()
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

        $writer = $reader->read('foobar\ClassName');
        // Assert that a empty second wrapper makes no difference
        $writer->apply(new NamespaceWrapper(''));
        $this->assertEquals(
            $expected,
            $writer->write()
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
