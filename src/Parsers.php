<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

const ST_KEEP = 1;
const ST_NEW = 2;
const ST_OLD = 3;
const ST_CHANGE = 4;
const ST_TEXT = [ST_KEEP => ' ',ST_NEW => '+',ST_OLD => '-'];

function parseFile(string $ext, string $content): object
{
    if ($ext == 'yml') {
        return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    return json_decode($content);
}

function compareChilds(array &$arr): void
{
    usort($arr, fn($a, $b)=>$a['key'] <=> $b['key']);
    foreach ($arr as $key => $elem) {
        if (array_key_exists('new', $elem) && array_key_exists('old', $elem)) {
            if ($elem['new'] === $elem['old']) {
                $arr[$key]['status'] = ST_KEEP;
            } else {
                $arr[$key]['status'] = ST_CHANGE;
            }
        } else {
            if (array_key_exists('new', $elem)) {
                $arr[$key]['status'] = ST_NEW;
            } elseif (array_key_exists('old', $elem)) {
                $arr[$key]['status'] = ST_OLD;
            } elseif (array_key_exists('child', $elem)) {
                    compareChilds($arr[$key]['child']);
            }
        }
    }
}

function makeDiff(object $arr1, object $arr2): array
{
    $diff = [];
    foreach ($arr1 as $key => $value) {
        if (property_exists($arr2, $key)) {
            if (is_object($value) && is_object($arr2->$key)) {
                $diff[] = ['key' => $key,'child' => makeDiff($value, $arr2->$key)];
            } else {
                $diff[] = ['key' => $key,'old' => $value,'new' => $arr2->$key];
            }
        } else {
            $diff[] = ['key' => $key,'old' => $value];
        }
    }
    foreach ($arr2 as $key => $value) {
        if (!property_exists($arr1, $key)) {
            $diff[] = ['key' => $key,'new' => $value];
        }
    }
    compareChilds($diff);
    return $diff;
}
