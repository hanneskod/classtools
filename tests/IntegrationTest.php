<?php

namespace hanneskod\classtools;

class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Should not trigger an error, se issue #10
     */
    public function testNullableTypes()
    {
        $this->assertTrue(
            !!new Transformer\Reader('<?php function someMethod(string $some_param) : ?string {return null;}')
        );
    }
}
