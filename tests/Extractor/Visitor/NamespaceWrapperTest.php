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

        $codeObj = $extractor->extract('\ClassName');
        $codeObj->registerVisitor(new NamespaceWrapper('NamespaceName'));
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }
}
