<?php
namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;

class NamespaceCrawlerTest extends \PHPUnit_Framework_TestCase
{
    public function testCrawlNamespaces()
    {
        $reader = new Reader(
<<<EOF
<?php
namespace {
    class ClassName
    {
        public function foobar()
        {
            new NamespaceCrawlerTest();
            new \hanneskod\classtools\Transformer\Action\NamespaceCrawlerTest();
        }
    }
}
EOF
        );

        $expected =
<<<EOF
namespace {
    class ClassName
    {
        public function foobar()
        {
            new \hanneskod\classtools\Transformer\Action\NamespaceCrawlerTest();
            new \hanneskod\classtools\Transformer\Action\NamespaceCrawlerTest();
        }
    }
}
EOF;

        $writer = new Writer;
        $writer->apply(new \PhpParser\NodeVisitor\NameResolver);
        $writer->apply(new NamespaceCrawler(array('hanneskod\classtools\Transformer\Action')));
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }

    public function testCrawlUnableToResolveNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new NamespaceCrawlerTest();
    }
}
EOF
        );

        $writer = new Writer;
        $writer->apply(new \PhpParser\NodeVisitor\NameResolver);
        $writer->apply(new NamespaceCrawler(['']));

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write($reader->read('ClassName'));
    }
}
