<?php
namespace hanneskod\classtools\Transformer;

class UseStmtTranslationTest extends \PHPUnit\Framework\TestCase
{
    public function testSaveNamespacedUseStatements()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace foo {
    use Exception;
    class ClassName
    {
    }
}
EOF
        );

        $expected =
<<<EOF
namespace foo {
    use Exception;
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer;

        $this->assertEquals(
            $expected,
            $writer->write($reader->read('foo\ClassName'))
        );
    }

    public function testSaveGlobalUseStatements()
    {
        $reader = new Reader(
<<<EOF
<?php
use random\Exception;
class ClassName
{
}
EOF
        );

        $expected =
<<<EOF
namespace {
    use random\Exception;
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer;

        $this->assertEquals(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
