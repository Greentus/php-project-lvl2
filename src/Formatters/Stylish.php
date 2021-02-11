<?php

namespace Differ\Formatters\Stylish;

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
    if (is_string($value)) {
        return $value;
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return var_export($value, true);
}

function genStylishObject(string $objKey, object $objElem, int $lvl = 0, bool $st = false): string
{
    $res = [];
    if ($st) {
        $res[] = '{';
    } else {
        $res[] = str_repeat(INDENT, ($lvl - 1) * 2) . $objKey . ': {';
    }
    foreach ($objElem as $key => $elem) {
        if (is_object($elem)) {
            $res[] = genStylishObject($key, $elem, $lvl + 1);
        } else {
            $res[] = str_repeat(INDENT, $lvl * 2) . $key . ': ' . toString($elem);
        }
    }
    $res[] = str_repeat(INDENT, ($lvl - 1) * 2) . '}';
    return implode(PHP_EOL, $res);
}

function genStylishElem(array $elem, int $lvl = 0): string
{
    $res = str_repeat(INDENT, $lvl * 2 - 1);
    if (array_key_exists('old', $elem)) {
        if (is_object($elem['old'])) {
            $old = genStylishObject($elem['key'], $elem['old'], $lvl + 1, true);
        } else {
            $old = toString($elem['old']);
        }
    } else {
        $old = '';
    }
    if (array_key_exists('new', $elem)) {
        if (is_object($elem['new'])) {
            $new = genStylishObject($elem['key'], $elem['new'], $lvl + 1, true);
        } else {
            $new = toString($elem['new']);
        }
    } else {
        $new = '';
    }
    switch ($elem['status']) {
        case ST_OLD:
            return $res .= ST_TEXT[$elem['status']] . ' ' . $elem['key'] . ': ' . $old;
        case ST_NEW:
            return $res .= ST_TEXT[$elem['status']] . ' ' . $elem['key'] . ': ' . $new;
        case ST_CHANGE:
            return $res .= ST_TEXT[ST_OLD] . ' ' . $elem['key'] . ': ' . $old . PHP_EOL .
                   str_repeat(INDENT, $lvl * 2 - 1) . ST_TEXT[ST_NEW] . ' ' . $elem['key'] . ': ' . $new;
        case ST_KEEP:
        default:
            return $res .= ST_TEXT[ST_KEEP] . ' ' . $elem['key'] . ': ' . $old ?? $new;
    }
}

function genStylish(array $childs, int $lvl = 0): string
{
    $res = [];
    if ($lvl == 0) {
        $res[] = '{';
        $arr = $childs;
    } else {
        $res[] = str_repeat(INDENT, ($lvl - 1) * 2 + 1)
                 . ST_TEXT[$childs['status'] ?? ST_KEEP]
                 . ' ' . $childs['key'] . ': {';
        $arr = $childs['child'];
    }
    foreach ($arr as $elem) {
        if (isset($elem['child'])) {
            $res[] = genStylish($elem, $lvl + 1);
        } else {
            $res[] = genStylishElem($elem, $lvl + 1);
        }
    }
    if ($lvl == 0) {
        $res[] = '}';
    } else {
        $res[] = str_repeat(INDENT, $lvl * 2) . '}';
    }
    return implode(PHP_EOL, $res);
}
