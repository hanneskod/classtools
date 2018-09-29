<?php

declare(strict_types = 1);

namespace hanneskod\classtools;

class ReadmeTest extends \hanneskod\readmetester\PHPUnit\ReadmeTestCase
{
    public function testReadmeExamples()
    {
        $this->assertReadme('README.md');
    }
}
