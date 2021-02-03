<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $ext, string $content): object
{
    if ($ext == 'yml') {
        return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    return json_decode($content);
}
