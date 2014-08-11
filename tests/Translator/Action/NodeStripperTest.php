<?php
namespace hanneskod\classtools\Translator\Action;

use hanneskod\classtools\Translator\Reader;

class NodeStripperTest extends \PHPUnit_Framework_TestCase
{
    public function testStripNodes()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace {
    class ClassName
    {
        public function foobar()
        {
            include "somefile.php";
            echo 'foobar';
        }
    }
}
EOF
        );

        $expected =
<<<EOF
namespace {
    class ClassName
    {
        public function foobar()
        {
            echo 'foobar';
        }
    }
}
EOF;

        $writer = $reader->read('ClassName');
        $writer->apply(new NodeStripper('PhpParser\Node\Expr\Include_'));
        $this->assertEquals(
            $expected,
            $writer->write()
        );
    }
}
