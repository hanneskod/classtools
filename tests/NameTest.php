<?php
namespace hanneskod\classtools;

class NameTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateNode()
    {
        $this->assertEquals(
            new \PhpParser\Node\Name(['']),
            (new Name(''))->createNode()
        );

        $this->assertEquals(
            new \PhpParser\Node\Name(['name', 'SPACE', 'ClassName']),
            (new Name('name\SPACE\ClassName'))->createNode()
        );

        $this->assertEquals(
            new \PhpParser\Node\Name(['', 'name', 'SPACE', 'ClassName']),
            (new Name('\name\SPACE\ClassName'))->createNode()
        );
    }

    public function testIsDefined()
    {
        $this->assertTrue((new Name('\hanneskod\classtools\NameTest'))->isDefined());
        $this->assertFalse((new Name('class\that\does\not\exist'))->isDefined());
    }

    public function testGetBasename()
    {
        $this->assertEquals(
            new Name('ClassName'),
            (new Name('name\SPACE\ClassName'))->getBasename()
        );

        $this->assertEquals(
            new Name('ClassName'),
            (new Name('ClassName'))->getBasename()
        );

        $this->assertEquals(
            new Name(''),
            (new Name(''))->getBasename()
        );
    }

    public function testGetNamespace()
    {
        $this->assertEquals(
            new Name('name\SPACE'),
            (new Name('name\SPACE\ClassName'))->getNamespace()
        );

        $this->assertEquals(
            new Name(''),
            (new Name('ClassName'))->getNamespace()
        );

        $this->assertEquals(
            new Name(''),
            (new Name(''))->getNamespace()
        );
    }

    public function testInNamespace()
    {
        $name = new Name('name\SPACE\ClassName');

        $this->assertTrue($name->inNamespace(new Name('name')));
        $this->assertTrue($name->inNamespace(new Name('\name')));
        $this->assertTrue($name->inNamespace(new Name('NAME')));
        $this->assertTrue($name->inNamespace(new Name('NAME\sPace')));

        $this->assertFalse($name->inNamespace(new Name('space')));
        $this->assertFalse($name->inNamespace(new Name('NAME\space\class')));
    }

    public function testNormalize()
    {
        $this->assertEquals(
            '',
            (new Name('\\'))->normalize()
        );
    }
}
