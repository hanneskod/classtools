<?php
namespace hanneskod\classtools\Extractor\Visitor;

use hanneskod\classtools\Extractor\Extractor;

class NamespaceCrawlerTest extends \PHPUnit_Framework_TestCase
{
    public function testCrawlNamespaces()
    {
        $extractor = new Extractor(
<<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new NamespaceCrawlerTest();
        new \hanneskod\classtools\Extractor\Visitor\NamespaceCrawlerTest();
    }
}
EOF
        );

        $expected =
<<<EOF
class ClassName
{
    public function foobar()
    {
        new \hanneskod\classtools\Extractor\Visitor\NamespaceCrawlerTest();
        new \hanneskod\classtools\Extractor\Visitor\NamespaceCrawlerTest();
    }
}
EOF;

        $codeObj = $extractor->extract('ClassName');
        $codeObj->registerVisitor(new \PhpParser\NodeVisitor\NameResolver);
        $codeObj->registerVisitor(new NamespaceCrawler(array('hanneskod\classtools\Extractor\Visitor')));
        $this->assertEquals(
            $expected,
            $codeObj->getCode()
        );
    }

    public function testCrawlUnableToResolveNamespace()
    {
        $extractor = new Extractor(
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

        $codeObj = $extractor->extract('ClassName');
        $codeObj->registerVisitor(new \PhpParser\NodeVisitor\NameResolver);
        $codeObj->registerVisitor(new NamespaceCrawler(array('')));

        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        $codeObj->getCode();
    }
}
