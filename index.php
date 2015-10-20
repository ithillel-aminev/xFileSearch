<?php

require 'src/Parser.php';
require 'src/Entity.php';

$p = new Parser();

if (!Parser::getValue('language')) {
    header('HTTP/1.0 400 Bad request');
    return;
}

header('Content-Type: application/json');
print(json_encode($p->parse()));

