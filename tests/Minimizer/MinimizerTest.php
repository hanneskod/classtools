<?php
namespace hanneskod\classtools\Minimizer;

class MinimizerTest extends \PHPUnit_Framework_TestCase
{
    public function testMinimize()
    {
        $map = $this->getMock('hanneskod\classtools\Iterator\ClassMapInterface');

        $map->expects($this->atLeastOnce())
            ->method('getClassMap')
            ->will($this->returnValue(array(__CLASS__ => __FILE__)));

        $minimizer = new Minimizer($map);

        $this->assertRegExp(
            '/class MinimizerTest/',
            $minimizer->minimize(),
            'The generated code should include MinimizerTest class'
        );
    }
}
