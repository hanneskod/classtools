<?php
namespace hanneskod\classtools\Extractor\Visitor;

use hanneskod\classtools\Extractor\Extractor;

class CommentStripperTest extends \PHPUnit_Framework_TestCase
{
    public function testStripComments()
    {
        $extractor = new Extractor(
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
class ClassName
{
    private \$var;
    public function test()
    {
        return true;
    }
}
EOF;

        $codeObj = $extractor->extract('ClassName');
        $codeObj->registerVisitor(new CommentStripper);
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }
}
