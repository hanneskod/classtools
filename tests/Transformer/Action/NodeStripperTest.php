<?php
namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;

class NodeStripperTest extends \PHPUnit\Framework\TestCase
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

        $writer = new Writer;
        $writer->apply(new NodeStripper('Stmt_Expression'));
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
