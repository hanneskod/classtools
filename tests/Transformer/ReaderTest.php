<?php

declare(strict_types = 1);

namespace hanneskod\classtools\Transformer;

class ReaderTest extends \PHPUnit\Framework\TestCase
{
    public function testFindDefinitions()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace foo;
class ClassName {}
interface InterfaceName {}
trait TraitName {}
enum EnumName {}
EOF
        );

        $this->assertEquals(
            [
                'foo\\ClassName',
                'foo\\InterfaceName',
                'foo\\TraitName',
                'foo\\EnumName',
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testHasDefinition()
    {
        $reader = new Reader("<?php class ClassName {}");
        $this->assertTrue($reader->hasDefinition('ClassName'));
        $this->assertTrue($reader->hasDefinition('\\ClassName'));

        $reader = new Reader("<?php namespace foo; class ClassName {}");
        $this->assertTrue($reader->hasDefinition('foo\\ClassName'));
        $this->assertTrue($reader->hasDefinition('\\foo\\ClassName'));
    }

    public function testFindBracketedDefinitions()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace foo {
    class ClassName {}
    class AnotherClassName {}
}
namespace bar {
    interface InterfaceName {}
}
namespace {
    trait TraitName {}
}
namespace baz {
    enum EnumName {}
}
EOF
        );

        $this->assertEquals(
            [
                'foo\\ClassName',
                'foo\\AnotherClassName',
                'bar\\InterfaceName',
                'TraitName',
                'baz\\EnumName',
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testFindGlobalDefinitions()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName {}
interface InterfaceName {}
EOF
        );

        $this->assertEquals(
            [
                'ClassName',
                'InterfaceName'
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testReadUndefinedClass()
    {
        $reader = new Reader('');
        $this->expectException('\hanneskod\classtools\Exception\RuntimeException');
        $reader->read('UndefinedClass');
    }

    public function testRead()
    {
        $reader = new Reader('<?php class FooBar {}');
        $this->assertIsArray(
            $reader->read('FooBar')
        );
    }

    public function testReadAll()
    {
        $reader = new Reader('');
        $this->assertIsArray(
            $reader->readAll()
        );
    }

    public function testSyntaxError()
    {
        $this->expectException('\hanneskod\classtools\Exception\ReaderException');
        new Reader('<?php functi hej(){}');
    }
}
