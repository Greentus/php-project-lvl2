<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Parsers\makeDiff;
use function Differ\Formatters\genStylish;

function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    if (!file_exists($path1)) {
        return "{\n  Error: file '{$path1}' not found.\n}";
    }
    $file1 = file_get_contents($path1);
    if ($file1 === false) {
        return "{\n  Error: file '{$path1}' read failed.\n}";
    }
    if (!file_exists($path2)) {
        return "{\n  Error: file '{$path2}' not found.\n}";
    }
    $file2 = file_get_contents($path2);
    if ($file2 === false) {
        return "{\n  Error: file '{$path2}' read failed.\n}";
    }
    $arr1 = parseFile(substr($path1, strrpos($path1, '.') + 1), $file1);
    if ($arr1 == null) {
        return "{\n  Error: parse file '{$path1}' fail.\n}";
    }
    $arr2 = parseFile(substr($path2, strrpos($path2, '.') + 1), $file2);
    if ($arr2 == null) {
        return "{\n  Error: parse file '{$path2}' fail.\n}";
    }
    $diff = makeDiff($arr1, $arr2);
    if ($format == 'stylish') {
        $res = genStylish($diff);
    } else {
        return "{\n  Error: invalid format '{$format}'.\n}";
    }
    return $res;
}
