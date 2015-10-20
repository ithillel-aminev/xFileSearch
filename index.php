<?php

//require 'autoload.php';
//
//use Taminev\Xfilessearch\Parser;


namespace Taminev\Xfilessearch;

require 'src/Parser.php';
require 'src/EntityFactory.php';
require 'src/Entity.php';
require 'src/Product.php';
require 'src/Video.php';
require 'src/Html.php';

$p = new Parser();

die($p->parse());


