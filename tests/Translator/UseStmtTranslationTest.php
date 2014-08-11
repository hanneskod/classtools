<?php
namespace hanneskod\classtools\Translator;

class UseStmtTranslationTest extends \PHPUnit_Framework_TestCase
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

        $this->assertEquals(
            $expected,
            $reader->read('foo\ClassName')->write()
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
namespace  {
    use random\Exception;
    class ClassName
    {
    }
}
EOF;

        $this->assertEquals(
            $expected,
            $reader->read('ClassName')->write()
        );
    }
}
