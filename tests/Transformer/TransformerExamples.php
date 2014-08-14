<?php
namespace hanneskod\classtools\Transformer;

/**
 * Transformer examples
 */
class TransformerExamples extends \hanneskod\exemplify\TestCase
{
    /**
     * Wrap code in namespace
     *
     * @expectOutputRegex #namespace Foo#
     */
    public function exampleWrapInNamespace()
    {
        $reader = new Reader("<?php class Bar {}");
        $writer = new Writer;
        $writer->apply(new Action\NamespaceWrapper('Foo'));

        // Outputs class Bar wrapped in namespace Foo
        echo $writer->write($reader->read('Bar'));
    }

    /**
     * Strip statements
     *
     * @expectOutputRegex #echo 'bar';#
     */
    public function exampleStripNodes()
    {
        $reader = new Reader("<?php require 'Foo.php'; echo 'bar';");
        $writer = new Writer;
        $writer->apply(new Action\NodeStripper('Expr_Include'));

        // Outputs the echo statement
        echo $writer->write($reader->readAll());
    }
}
