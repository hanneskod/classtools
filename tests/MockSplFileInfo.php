<?php
namespace hanneskod\classtools\Tests;

class MockSplFileInfo extends \hanneskod\classtools\Iterator\SplFileInfo
{
    public function __construct($contents)
    {
        $this->contents = $contents;
        $tempnam = tempnam(sys_get_temp_dir(), 'CLASSTOOLS_');
        unlink($tempnam);
        $this->path = $tempnam . '.php';
        $handle = fopen($this->path, "w");
        fwrite($handle, $contents);
        fclose($handle);
    }

    public function __destruct()
    {
        unlink($this->path);
    }

    public function getPathname()
    {
        return $this->path;
    }

    public function getRealPath()
    {
        return $this->path;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
