<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\genJson;
use function Differ\Formatters\Plain\genPlain;
use function Differ\Formatters\Stylish\genStylish;

const FM_JSON = 'json';
const FM_PLAIN = 'plain';
const FM_STYLISH = 'stylish';

function formatDiff(array $diff, string $format): string
{
    switch ($format) {
        case FM_PLAIN:
            return genPlain($diff);
        case FM_JSON:
            return genJson($diff);
        case FM_STYLISH:
        default:
            return genStylish($diff);
    }
}
