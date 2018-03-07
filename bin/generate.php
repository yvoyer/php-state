#!/usr/bin/env php
<?php

namespace Star;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Tool\Imaging\Adapter\PHPGdProcessor;
use Star\Component\State\Tool\Imaging\ImageGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$machine = StateBuilder::build()
    ->allowTransition('t1', 's1', 's2')
    ->allowTransition('t2', 's3', 's4')
    ->allowTransition('t3', 's5', 's6')
    ->allowTransition('t4', 's7', 's8')
    ->allowTransition('t5', 's9', 's10')
    ->create('s4');
;

$writer = new ImageGenerator(new PHPGdProcessor());

var_dump(
    $writer->generate('/tmp/image-state.png', $machine)
);
