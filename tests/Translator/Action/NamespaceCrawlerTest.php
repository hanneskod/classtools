<?php
namespace hanneskod\classtools\Translator\Action;

use hanneskod\classtools\Translator\Reader;

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
            new \hanneskod\classtools\Translator\Action\NamespaceCrawlerTest();
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
            new \hanneskod\classtools\Translator\Action\NamespaceCrawlerTest();
            new \hanneskod\classtools\Translator\Action\NamespaceCrawlerTest();
        }
    }
}
EOF;

        $writer = $reader->read('ClassName');
        $writer->apply(new \PhpParser\NodeVisitor\NameResolver);
        $writer->apply(new NamespaceCrawler(array('hanneskod\classtools\Translator\Action')));
        $this->assertEquals(
            $expected,
            $writer->write()
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

        $writer = $reader->read('ClassName');
        $writer->apply(new \PhpParser\NodeVisitor\NameResolver);
        $writer->apply(new NamespaceCrawler(['']));

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write();
    }
}
