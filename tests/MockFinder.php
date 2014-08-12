<?php
namespace hanneskod\classtools\Tests;

class MockFinder extends \Symfony\Component\Finder\Finder
{
    private static $iterator;

    public static function setIterator(\Traversable $iterator)
    {
        self::$iterator = $iterator;
    }

    public function getIterator()
    {
        return self::$iterator;
    }
}
