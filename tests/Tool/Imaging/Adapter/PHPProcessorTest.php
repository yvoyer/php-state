<?php

namespace Star\Component\State\Tool\Imaging\Adapter;

use PHPUnit\Framework\TestCase;

final class PHPProcessorTest extends TestCase
{
    /**
     * @var PHPGdProcessor
     */
    private $_pHPProcessor;

    public function setUp()
    {
        $this->_pHPProcessor = new PHPGdProcessor();
    }

    public function test_it_should_do_something()
    {
        $this->fail('test someting');
    }
}
