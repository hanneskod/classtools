<?php
namespace hanneskod\classtools\Extractor\Visitor;

use hanneskod\classtools\Extractor\Extractor;

class NamespaceWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testWrapCodeInNamespace()
    {
        $extractor = new Extractor(
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

        $codeObj = $extractor->extract('ClassName');
        $codeObj->registerVisitor(new NamespaceWrapper('NamespaceName'));
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }

    public function testExtendNamespace()
    {
        $extractor = new Extractor(
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

        $codeObj = $extractor->extract('NamespaceName\ClassName');
        $codeObj->registerVisitor(new NamespaceWrapper('extended'));
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }

    public function testEnforceNamespace()
    {
        $extractor = new Extractor(
<<<EOF
<?php
class ClassName
{
}
EOF
        );

        $expected =
<<<EOF
namespace  {
    class ClassName
    {
    }
}
EOF;

        $codeObj = $extractor->extract('ClassName');
        $codeObj->registerVisitor(new NamespaceWrapper);
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }

    public function testIgnoreExtendedEmptyNamespace()
    {
        $extractor = new Extractor(
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

        $codeObj = $extractor->extract('foobar\ClassName');
        // Assert that a empty second wrapper makes no difference
        $codeObj->registerVisitor(new NamespaceWrapper);
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }
}
