<?php

namespace Provide\Representation;

use BEAR\Resource\JsonRenderer;
use BEAR\Resource\RenderInterface;
use BEAR\Sunday\Provide\Representation\RepresentationModule;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector;

class RepresentationModuleTest extends TestCase
{
    public function testRenderer()
    {
        $renderer = (new Injector(new RepresentationModule))->getInstance(RenderInterface::class);
        $this->assertInstanceOf(JsonRenderer::class, $renderer);
    }
}
