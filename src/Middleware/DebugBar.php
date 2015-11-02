<?php

namespace Psr7Middlewares\Middleware;

use DebugBar\DebugBar as Bar;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware to render a debugbar in html responses.
 */
class DebugBar
{
    protected $debugBar;

    /**
     * Constructor. Set the debug bar.
     *
     * @param Bar|null $debugBar
     */
    public function __construct(Bar $debugBar = null)
    {
        if ($debugBar !== null) {
            $this->debugBar($debugBar);
        }
    }

    /**
     * Set the debug bar.
     *
     * @param Bar $debugBar
     * 
     * @return self
     */
    public function debugBar(Bar $debugBar)
    {
        $this->debugBar = $debugBar;

        return $this;
    }

    /**
     * Execute the middleware.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $next($request, $response);

        if (strtolower(pathinfo($request->getUri()->getPath(), PATHINFO_EXTENSION)) === 'html' || strpos($header, 'html') !== false) {
            if ($this->debugBar === null) {
                throw new RuntimeException('No DebugBar instance has been provided');
            }

            $renderer = $this->debugBar->getJavascriptRenderer();

            ob_start();
            echo '<style>';
            $renderer->dumpCssAssets();
            echo '</style>';

            echo '<script>';
            $renderer->dumpJsAssets();
            echo '</script>';

            echo $renderer->render();

            $response->getBody()->write(ob_get_clean());
        }

        return $response;
    }
}
