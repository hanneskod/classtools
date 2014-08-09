<?php
namespace hanneskod\classtools\Extractor;

class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testFindDefinitions()
    {
        $extractor = new Extractor(
<<<EOF
<?php
namespace foo;
class ClassName {}
interface InterfaceName {}
trait TratName {}
EOF
        );

        $expected = array(
            'foo\ClassName',
            'foo\InterfaceName',
            'foo\TratName'
        );

        $this->assertTrue($extractor->hasDefinition('foo\ClassName'));
        $this->assertEquals($expected, $extractor->getDefinitionNames());
    }

    public function testFindBracketedDefinitions()
    {
        $extractor = new Extractor(
<<<EOF
<?php
namespace foo {
    class ClassName {}
}
namespace bar {
    interface InterfaceName {}
}
namespace {
    trait TratName {}
}
EOF
        );

        $expected = array(
            'foo\ClassName',
            'bar\InterfaceName',
            'TratName'
        );

        $this->assertEquals($expected, $extractor->getDefinitionNames());
    }

    public function testFindGlobalDefinitions()
    {
        $extractor = new Extractor(
<<<EOF
<?php
class ClassName {}
interface InterfaceName {}
EOF
        );

        $expected = array(
            'ClassName',
            'InterfaceName'
        );

        $this->assertEquals($expected, $extractor->getDefinitionNames());
    }

    public function testExtractUndefinedClass()
    {
        $extractor = new Extractor('');
        $this->setExpectedException('\hanneskod\classtools\Exception\RuntimeException');
        $extractor->extract('UndefinedClass');
    }

    public function testExtract()
    {
        $extractor = new Extractor('<?php class FooBar {}');
        $this->assertInstanceOf(
            '\hanneskod\classtools\Extractor\CodeObject',
            $extractor->extract('FooBar')
        );
    }

    public function testExtractAll()
    {
        $extractor = new Extractor('');
        $this->assertInstanceOf(
            '\hanneskod\classtools\Extractor\CodeObject',
            $extractor->extractAll()
        );
    }
}
