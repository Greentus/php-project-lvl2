<?php

namespace Differ\DifferTest;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testCompareJson(): void
    {
        $fs = vfsStream::setup('test');
        $dir = vfsStream::url('test');
        $file1 = $dir . '/file1.json';
        $json1 = <<<'FILE1'
{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22",
  "follow": false
}
FILE1;
        $file2 = $dir . '/file2.json';
        $json2 = <<<'FILE2'
{
  "timeout": 20,
  "verbose": true,
  "host": "hexlet.io"
}
FILE2;
        file_put_contents($file1, $json1);
        file_put_contents($file2, $json2);
        $diff = genDiff($file1, $file2);
        $res = <<<'RESULT'
{
 - follow: false
   host: 'hexlet.io'
 - proxy: '123.234.53.22'
 - timeout: 50
 + timeout: 20
 + verbose: true
}
RESULT;
        $this->assertEquals($res, $diff);
    }

    public function testCompareYaml(): void
    {
        $fs = vfsStream::setup('test');
        $dir = vfsStream::url('test');
        $file1 = $dir . '/file1.yml';
        $yaml1 = <<<'FILE1'
---
host: hexlet.io
timeout: 50
proxy: 123.234.53.22
follow: false
FILE1;
        $file2 = $dir . '/file2.yml';
        $yaml2 = <<<'FILE2'
---
timeout: 20
verbose: true
host: hexlet.io
FILE2;
        file_put_contents($file1, $yaml1);
        file_put_contents($file2, $yaml2);
        $diff = genDiff($file1, $file2);
        $res = <<<'RESULT'
{
 - follow: false
   host: 'hexlet.io'
 - proxy: '123.234.53.22'
 - timeout: 50
 + timeout: 20
 + verbose: true
}
RESULT;
        $this->assertEquals($res, $diff);
    }
}
