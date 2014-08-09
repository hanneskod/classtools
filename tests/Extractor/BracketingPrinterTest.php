<?php
namespace hanneskod\classtools\Extractor;

use PhpParser\Parser;
use PhpParser\Lexer;

class BracketingPrinterTest extends \PHPUnit_Framework_TestCase
{
    public function testPrintWithBrackets()
    {
        $parser = new Parser(new Lexer);
        $printer = new BracketingPrinter;

        $stmts = $parser->parse(
<<<EOF
<?php
namespace foo;
class Bar
{
}
EOF
        );

        $expected =
<<<EOF
namespace foo {
    class Bar
    {
    }
}
EOF;

        $this->assertEquals($expected, $printer->prettyPrint($stmts));
    }
}
