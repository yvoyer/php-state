#!/usr/bin/env php
<?php

namespace Star;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Tool\Imaging\Adapter\PHPGdProcessor;
use Star\Component\State\Tool\Imaging\ImageGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$machine = StateBuilder::build()
    ->allowTransition('pass', 'locked', 'unlocked')
    ->allowTransition('reset', ['violation', 'unlock'], 'locked')
    ->allowTransition('alarm', 'locked', 'violation')
    ->create('locked');
;

$writer = new ImageGenerator(new PHPGdProcessor());

var_dump(
    $writer->generate('/tmp/image-state.png', $machine)
);
