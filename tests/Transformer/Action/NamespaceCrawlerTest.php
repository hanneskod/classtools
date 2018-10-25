<?php
namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;

class NamespaceCrawlerTest extends \PHPUnit\Framework\TestCase
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
        $writer->apply(new NameResolver);
        $writer->apply(new NamespaceCrawler(['\hanneskod\classtools\Transformer\Action']));
        $this->assertEquals(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }

    public function testCrawlUnableToResolveNamespaceException()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer;
        $writer->apply(new NameResolver);
        $writer->apply(new NamespaceCrawler(['']));

        // NonExistingClass does not resolve
        $this->expectException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write($reader->read('ClassName'));
    }

    public function testWhitelistNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new \whitelist\NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer;
        $writer->apply(new NameResolver);
        $writer->apply(new NamespaceCrawler([''], ['whitelist']));

        // NonExistingClass does not resolve, but no exception is thrown
        $this->assertTrue(is_string($writer->write($reader->read('ClassName'))));
    }
}
