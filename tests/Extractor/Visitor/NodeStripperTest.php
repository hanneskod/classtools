<?php
namespace hanneskod\classtools\Extractor\Visitor;

use hanneskod\classtools\Extractor\Extractor;

class NodeStripperTest extends \PHPUnit_Framework_TestCase
{
    public function testStripNodes()
    {
        $extractor = new Extractor(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        include "somefile.php";
        echo 'foobar';
    }
}
EOF
        );

        $expected =
<<<EOF
class ClassName
{
    public function foobar()
    {
        echo 'foobar';
    }
}
EOF;

        $codeObj = $extractor->extract('\ClassName');
        $codeObj->registerVisitor(new NodeStripper('PhpParser\Node\Expr\Include_'));
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }
}
