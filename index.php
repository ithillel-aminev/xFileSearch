<?php

require 'src/Parser.php';
require 'src/Entity.php';

$p = new Parser();

if (!Parser::getValue('language')) {
    header('HTTP/1.0 400 Bad request');
} else {
    header('HTTP/1.0 200 OK');
}
header('Content-Type: application/json');

die(json_encode($p->parse()));

