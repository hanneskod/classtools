<?php
namespace hanneskod\classtools\Translator;

/**
 * Translator examples
 */
class TranslatorExamples extends \hanneskod\exemplify\TestCase
{
    /**
     * Wrap code in namespace
     *
     * @expectOutputRegex #namespace Foo#
     */
    public function exampleWrapInNamespace()
    {
        $reader = new Reader("<?php class Bar {}");

        // Outputs class Bar wrapped in namespace Foo
        echo $reader->read('Bar')
            ->apply(new Action\NamespaceWrapper('Foo'))
            ->write();
    }

    /**
     * Strip statements
     *
     * @expectOutputRegex #echo 'bar';#
     */
    public function exampleStripNodes()
    {
        $reader = new Reader("<?php require 'Foo.php'; echo 'bar';");

        // Outputs the echo statement
        echo $reader->readAll()
            ->apply(new Action\NodeStripper('PhpParser\Node\Expr\Include_'))
            ->write();
    }
}
