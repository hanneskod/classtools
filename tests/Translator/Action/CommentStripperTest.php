<?php
namespace hanneskod\classtools\Translator\Action;

use hanneskod\classtools\Translator\Reader;

class CommentStripperTest extends \PHPUnit_Framework_TestCase
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
namespace  {
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

        $writer = $reader->read('ClassName');
        $writer->apply(new CommentStripper);
        $this->assertEquals(
            $expected,
            $writer->write()
        );
    }
}
