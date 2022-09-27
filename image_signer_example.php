<?php
require __DIR__ . '/vendor/autoload.php';

use DenisPm\ImageSigner\Constants\Colors;
use DenisPm\ImageSigner\Constants\Fonts;
use DenisPm\ImageSigner\ImageBlank;


const EXAMPLE_TEXT = <<<EOL
Lorem ipsum dolor sit amet, 
consectetur adipisicing elit. 
Ad aspernatur dolores excepturi iste iure, 
molestias necessitatibus neque 
officia porro voluptate.
EOL;

$blank = new ImageBlank(__DIR__ . '/example.jpg');
$blank
    ->addString(
        "Welcome to ImageSigner!",
        36,
        round($blank->getXSize() * 0.5),
        round($blank->getYSize() * 0.2),
        Fonts::ARIAL_BLACK,
        0,
        Colors::GREEN
    )
    ->addStringBlock(
        EXAMPLE_TEXT,
        16,
        round($blank->getXSize() * 0.5),
        round($blank->getYSize() * 0.5),
        Fonts::CALIBRI,
        200,
        true,
        0,
        45,
        Colors::WHITE,
        0.9
    )
    ->show();
