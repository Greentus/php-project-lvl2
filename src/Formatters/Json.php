<?php

namespace Differ\Formatters\Json;

function genJson(array $childs): string
{
    return json_encode($childs, JSON_PRETTY_PRINT);
}
