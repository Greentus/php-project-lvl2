<?php

namespace Differ\Formatters\Plain;

const INDENT = '  ';

use const Differ\Parsers\ST_KEEP;
use const Differ\Parsers\ST_NEW;
use const Differ\Parsers\ST_OLD;
use const Differ\Parsers\ST_CHANGE;
use const Differ\Parsers\ST_TEXT;

/**
* @param mixed $value
*/
function toString($value): string
{
    if (is_object($value)) {
        return '[complex value]';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return var_export($value, true);
}

function genPlainElem(array $elem, string $parent = ''): string
{
    if (array_key_exists('old', $elem)) {
            $old = toString($elem['old']);
    } else {
        $old = '';
    }
    if (array_key_exists('new', $elem)) {
            $new = toString($elem['new']);
    } else {
        $new = '';
    }
    $parent .= empty($parent) ? '' : '.';
    switch ($elem['status']) {
        case ST_OLD:
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was removed';
        case ST_NEW:
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was added with value: ' . $new;
        case ST_CHANGE:
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was updated. From '
                          . $old . ' to ' . $new;
        case ST_KEEP:
        default:
            return '';
    }
}

function genPlain(array $childs, string $parent = ''): string
{
    $res = [];
    if ($parent == '') {
        $arr = $childs;
    } else {
        $arr = $childs['child'];
    }
    foreach ($arr as $elem) {
        if (isset($elem['child'])) {
            $str = genPlain($elem, $parent . (empty($parent) ? '' : '.') . $elem['key']);
            if (!empty($str)) {
                $res[] = $str;
            }
        } else {
            $str = genPlainElem($elem, $parent);
            if (!empty($str)) {
                $res[] = $str;
            }
        }
    }
    return implode(PHP_EOL, $res);
}
