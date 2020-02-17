<?php

namespace Butschster\GitHooks\Contracts;

use Closure;

interface MessageHook
{
    /**
     * @param string $message
     * @param Closure $next
     */
    public function handle(string $message, Closure $next);
}
