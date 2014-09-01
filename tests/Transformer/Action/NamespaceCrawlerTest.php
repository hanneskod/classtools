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
namespace  {
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
        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $writer->write($reader->read('ClassName'));
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
        new NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer;
        $writer->apply(new NameResolver);
        $writer->apply(new NamespaceCrawler([''], [], false));

        // NonExistingClass does not resolve, but no exception is thrown
        $this->assertTrue(is_string($writer->write($reader->read('ClassName'))));
    }

    public function testIgnoreNamespace()
    {
        $reader = new Reader(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new \ignore\NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer;
        $writer->apply(new NameResolver);
        $writer->apply(new NamespaceCrawler([''], ['ignore']));

        // NonExistingClass does not resolve, but is ignored since it is in the 'ignore' namespace
        $this->assertTrue(is_string($writer->write($reader->read('ClassName'))));
    }
}
