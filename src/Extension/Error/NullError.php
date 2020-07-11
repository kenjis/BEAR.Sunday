<?php

declare(strict_types=1);

namespace BEAR\Sunday\Extension\Error;

use BEAR\Sunday\Extension\Router\RouterMatch as Request;
use Exception;

final class NullError implements ErrorInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e, Request $request)
    {
        return $this;
    }

    public function transfer(): void
    {
    }
}
