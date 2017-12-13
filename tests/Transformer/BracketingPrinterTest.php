<?php namespace hanneskod\classtools\Transformer;

use PhpParser\ParserFactory;

class BracketingPrinterTest extends \PHPUnit\Framework\TestCase
{
    public function testPrintWithBrackets()
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP5);
        $printer = new BracketingPrinter;

        $stmts = $parser->parse(
            <<<EOF
<?php namespace foo;
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
