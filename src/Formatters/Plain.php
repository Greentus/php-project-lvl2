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
    if (is_string($value)) {
        return "'" . $value . "'";
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
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was removed' . PHP_EOL;
        case ST_NEW:
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was added with value: '
                          . $new . PHP_EOL;
        case ST_CHANGE:
            var_dump($elem);
            return $res = 'Property \'' . $parent . $elem['key'] . '\' was updated. From '
                          . $old . ' to ' . $new . PHP_EOL;
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
            $res[] = genPlain($elem, $parent . (empty($parent) ? '' : '.') . $elem['key']);
        } else {
            $res[] = genPlainElem($elem, $parent);
        }
    }
    return implode('', $res);
}
