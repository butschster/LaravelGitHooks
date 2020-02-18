<?php

namespace Butschster\GitHooks;

use Butschster\GitHooks\Contracts\Hook;
use Butschster\GitHooks\Exceptions\HookFailException;
use Closure;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class HooksPipeline extends Pipeline
{
    /**
     * @var Closure
     */
    protected $callback;

    /**
     * @var Closure
     */
    protected $errorCallback;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $hook;

    /**
     * @param \Illuminate\Contracts\Container\Container|null $container
     * @param Repository $config
     * @param string $hook
     */
    public function __construct(Container $container, Repository $config, string $hook)
    {
        $this->container = $container;
        $this->config = $config;
        $this->hook = $hook;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function withCallback(Closure $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function withErrorCallback(Closure $callback)
    {
        $this->errorCallback = $callback;

        return $this;
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    if (is_callable($pipe)) {
                        // If the pipe is an instance of a Closure, we will just call it directly but
                        // otherwise we'll resolve the pipes out of the container and call it with
                        // the appropriate method and arguments, returning the results back out.
                        return $pipe($passable, $stack);
                    } elseif (! is_object($pipe)) {
                        $config = (array) $this->config->get('git_hooks.'.$this->hook.'.'.$pipe);

                        // If the pipe is a string we will parse the string and resolve the class out
                        // of the dependency injection container. We can then build a callable and
                        // execute the pipe function giving in the parameters that are required.
                        $pipe = $this->getContainer()->make($pipe, ['config' => $config]);

                        if ($this->callback) {
                            call_user_func_array($this->callback, [$pipe]);
                        }

                        $parameters = [$passable, $stack];
                    } else {
                        // If the pipe is already an object we'll just make a callable and pass it to
                        // the pipe as-is. There is no need to do any extra parsing and formatting
                        // since the object we're given was already a fully instantiated object.
                        $parameters = [$passable, $stack];
                    }

                    $carry = method_exists($pipe, $this->method)
                        ? $pipe->{$this->method}(...$parameters)
                        : $pipe(...$parameters);

                    return $this->handleCarry($carry);
                } catch (Exception $e) {
                    $this->handleExceptionCallback($pipe, $e);
                    $this->handleException($passable, $e);
                } catch (Throwable $e) {
                    $this->handleExceptionCallback($pipe, $e);
                    $this->handleException($passable, $e);
                }
            };
        };
    }

    /**
     * @param Hook $hook
     * @param Exception $e
     */
    protected function handleExceptionCallback(Hook $hook, $e)
    {
        if ($this->errorCallback) {
            call_user_func_array($this->errorCallback, [$hook, $e]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function handleException($passable, $e)
    {
        throw new HookFailException($e->getMessage(), 0, $e);
    }
}
