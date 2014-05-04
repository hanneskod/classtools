<?php
namespace hanneskod\classtools\Minimizer;

class ClassMinimizerTest extends \PHPUnit_Framework_TestCase
{
    public function testMinimize()
    {
        $minimizer = new ClassMinimizer(
            new \ReflectionClass('hanneskod\classtools\Minimizer\ClassMinimizer')
        );
        $this->assertRegExp(
            '/public function getPhpCode/',
            (string)$minimizer,
            'The generated code should include method getPhpCode'
        );
    }
}
