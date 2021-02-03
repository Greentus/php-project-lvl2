<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
use function Differ\Parsers\parse;

function genDiff(string $path1, string $path2): string
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
    $arr1 = parse(substr($path1, strrpos($path1, '.') + 1), $file1);
    if ($arr1 == null) {
        return "{\n  Error: parse file '{$path1}' fail.\n}";
    }
    $arr2 = parse(substr($path2, strrpos($path2, '.') + 1), $file2);
    if ($arr2 == null) {
        return "{\n  Error: parse file '{$path2}' fail.\n}";
    }
    $res = [];
    foreach ((array) $arr1 as $key => $value) {
        if (isset($arr2->$key)) {
            $res[] = ['key' => $key,'value' => var_export($value, true), 'new' => var_export($arr2->$key, true)];
        } else {
            $res[] = ['key' => $key,'value' => var_export($value, true)];
        }
    }
    foreach ((array) $arr2 as $key => $value) {
        if (!isset($arr1->$key)) {
            $res[] = ['key' => $key,'new' => var_export($value, true)];
        }
    }
    $res = sortBy($res, fn($elem)=>$elem['key']);
    $res = array_reduce($res, function ($acc, $elem) {
        if (isset($elem['value']) && isset($elem['new'])) {
            if ($elem['value'] == $elem['new']) {
                $acc[] = "  {$elem['key']}: {$elem['value']}";
            } else {
                $acc[] = "- {$elem['key']}: {$elem['value']}";
                $acc[] = "+ {$elem['key']}: {$elem['new']}";
            }
        } elseif (isset($elem['value'])) {
            $acc[] = "- {$elem['key']}: {$elem['value']}";
        } else {
            $acc[] = "+ {$elem['key']}: {$elem['new']}";
        }
        return $acc;
    }, []);
    $res = implode("\n ", $res);
    return "{\n {$res}\n}";
}
