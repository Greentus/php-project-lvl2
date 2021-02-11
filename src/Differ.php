<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Parsers\makeDiff;
use function Differ\Formatters\formatDiff;

use const Differ\Formatters\FM_STYLISH;

function genDiff(string $path1, string $path2, string $format = FM_STYLISH): string
{
    if (!file_exists($path1)) {
        return "Error: file '{$path1}' not found.";
    }
    $file1 = file_get_contents($path1);
    if ($file1 === false) {
        return "Error: file '{$path1}' read failed.";
    }
    if (!file_exists($path2)) {
        return "Error: file '{$path2}' not found.";
    }
    $file2 = file_get_contents($path2);
    if ($file2 === false) {
        return "Error: file '{$path2}' read failed.";
    }
    $arr1 = parseFile(substr($path1, strrpos($path1, '.') + 1), $file1);
    if ($arr1 == null) {
        return "Error: parse file '{$path1}' fail.";
    }
    $arr2 = parseFile(substr($path2, strrpos($path2, '.') + 1), $file2);
    if ($arr2 == null) {
        return "Error: parse file '{$path2}' fail.";
    }
    $diff = makeDiff($arr1, $arr2);
    $res = formatDiff($diff, $format);
    return $res;
}
