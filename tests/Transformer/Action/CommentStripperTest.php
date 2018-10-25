<?php
namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;

class CommentStripperTest extends \PHPUnit\Framework\TestCase
{
    public function testStripComments()
    {
        $reader = new Reader(
<<<EOF
<?php
/**
 * File docblock comment
 */

/**
 * Class docblock
 */
class ClassName
{
    /**
     * @var string Some desc
     */
    private \$var;

    /**
     * Some docblock here too
     */
    public function test()
    {
        // inline comment
        return true; // comment at end of line
        /*
            Comment
        */
        # Comment...
    }
}
EOF
        );

        $expected =
<<<EOF
namespace {
    class ClassName
    {
        private \$var;
        public function test()
        {
            return true;
            
        }
    }
}
EOF;

        $writer = new Writer;
        $writer->apply(new CommentStripper);
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
