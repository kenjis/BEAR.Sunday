<?php
/**
 * This file is part of the BEAR.Sunday package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Sunday\Inject;

use Psr\Log\LoggerInterface;

/**
 * PSR-logger setter
 */
trait PsrLoggerInject
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PSR Logger setter
     *
     * @param LoggerInterface $logger
     *
     * @Ray\Di\Di\Inject
     */
    public function setPsrLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
