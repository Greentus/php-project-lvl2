<?php

namespace Differ\Formatters\Json;

function genJson(array $childs): string
{
    $res = json_encode($childs, JSON_PRETTY_PRINT);
    return $res === false ? '{}' : $res;
}
