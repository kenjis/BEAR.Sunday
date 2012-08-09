<?php
/**
 * BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Web;

use BEAR\Framework\Exception\InvalidResourceType;
use BEAR\Framework\Inject\LogInject;
use BEAR\Framework\Application\LoggerInterface as AppLogger;
use BEAR\Framework\Output\ConsoleInterface;
use BEAR\Resource\Request;
use BEAR\Resource\LoggerInterface;
use BEAR\Resource\Logger;
use BEAR\Resource\Object as ResourceObject;
use BEAR\Framework\Resource\AbstractPage as Page;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Helper\FormatterHelper as Formatter;
use Ray\Aop\Weave;
use Ray\Di\Di\Inject;
use Exception;
use Traversable;


/**
 * Output with using symfony HttpFoundation
 *
 * @package    BEAR.Framework
 * @subpackage Web
 */
class SymfonyResponse implements ResponseInterface
{
    use LogInject;

    /**
     * Exception
     *
     * @var Exception
     */
    private $e;

    /**
     * Resource object
     *
     * @var BResourceObject
     */
    private $resource;

    /**
     * Response resource object
     *
     * @var Response
     */
    private $response;

    /**
     * Mode
     *
     * @param string $mode
     */
    private $mode;

    private $code;
    private $headers;
    private $view;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    /**
     * Set application logger
     *
     * @param Logger $logger
     *
     * @Inject
     */
    public function setAppLogger(AppLogger $appLogger)
    {
        $this->appLogger = $appLogger;
    }

    /**
     * @param BEAR\Framework\Output\Cli $cliOutput
     *
     * @Inject
     */
    public function __construct(ConsoleInterface $consoleOutput){
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * Set Resource
     *
     * @param mixed $resource BEAR\Rsource\Object | Ray\Aop\Weaver $resource
     *
     * @throws InvalidResourceType
     * @return \BEAR\Framework\Web\SymfonyResponse
     */
    public function setResource($resource)
    {
        if ($resource instanceof Weave) {
            $resource = $resource->___getObject();
        }
        if ($resource instanceof ResourceObject === false && $resource instanceof Weave === false) {
            $type = (is_object($resource)) ? get_class($resource) : gettype($resource);
            throw new InvalidResourceType($type);
        }
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set Excpection
     *
     * @param Exception $e
     *
     * @return \BEAR\Framework\Web\SymfonyResponse
     */
    public function setException(Exception $e, $exceptionId)
    {
        $this->e = $e;
        $this->code = $e->getCode();
        $this->headers = [];
        $this->body = $exceptionId;

        return $this;
    }

    /**
     * Render
     *
     * @param Callback $renderer
     *
     * @return self
     */
    public function render(Callback $renderer = null)
    {
        if (is_callable($renderer)) {
            $this->view = $renderer($this->body);
        } else {
            $this->view = (string) $this->resource;
        }

        return $this;
    }

    /**
     * Make responce object with RFC 2616 compliant HTTP header
     *
     * @return \BEAR\Framework\Web\SymfonyResponse
     */
    public function prepare()
    {
        $this->response = new Response($this->view, $this->resource->code, (array) $this->resource->headers);
        // compliant with RFC 2616.
        $this->response->prepare();

        return $this;
    }

    /**
     * Output web console log (FireBug + FirePHP)
     *
     * @return \BEAR\Framework\Web\SymfonyResponse
     */
    public function outputWebConsoleLog()
    {
        $this->appLogger->outputWebConsoleLog();
        return $this;
    }

    /**
     * Transfer representational state to http client (or consoleoutput)
     *
     * @return \BEAR\Framework\Web\SymfonyResponse
     */
    public function send()
    {
        if (PHP_SAPI === 'cli') {
            //$this->sendCli();
            if ($this->resource instanceof Page) {
                $this->resource->headers = $this->response->headers->all();
            }
            $statusText = Response::$statusTexts[$this->resource->code];
            $this->consoleOutput->send($this->resource, $this->e, $statusText);
        } else {
            $this->response->send();
        }

        return $this;
    }
}
