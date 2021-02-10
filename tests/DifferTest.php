<?php

namespace Differ\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testCompareJsonRecursive(): void
    {
        $file1 = 'tests/fixtures/step6/file1.json';
        $file2 = 'tests/fixtures/step6/file2.json';
        $diff = genDiff($file1, $file2);
        $this->assertStringEqualsFile('tests/fixtures/step6/res.diff', $diff);
    }

    public function testCompareYamlRecursive(): void
    {
        $file1 = 'tests/fixtures/step6/file1.yml';
        $file2 = 'tests/fixtures/step6/file2.yml';
        $diff = genDiff($file1, $file2);
        $this->assertStringEqualsFile('tests/fixtures/step6/res.diff', $diff);
    }

    public function testCompareJson(): void
    {
        $file1 = 'tests/fixtures/step3/file1.json';
        $file2 = 'tests/fixtures/step3/file2.json';
        $diff = genDiff($file1, $file2);
        $this->assertStringEqualsFile('tests/fixtures/step3/res.diff', $diff);
    }

    public function testCompareYaml(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml';
        $file2 = 'tests/fixtures/step5/file2.yml';
        $diff = genDiff($file1, $file2);
        $this->assertStringEqualsFile('tests/fixtures/step5/res.diff', $diff);
    }
}
