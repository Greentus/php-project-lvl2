<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;

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
    $arr1 = json_decode($file1, true);
    if ($arr1 == null) {
        return "{\n  Error: json_decode file '{$path1}' fail " . json_last_error_msg() . "\n}";
    }
    $arr2 = json_decode($file2, true);
    if ($arr2 == null) {
        return "{\n  Error: json_decode file '{$path2}' fail " . json_last_error_msg() . "\n}";
    }
    $res = [];
    foreach ($arr1 as $key => $value) {
        if (array_key_exists($key, $arr2)) {
            $res[] = ['key' => $key,'value' => var_export($value, true), 'new' => var_export($arr2[$key], true)];
        } else {
            $res[] = ['key' => $key,'value' => var_export($value, true)];
        }
    }
    foreach ($arr2 as $key => $value) {
        if (!array_key_exists($key, $arr1)) {
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
    $res=implode("\n ", $res);
    return "{\n {$res}\n}";
}
