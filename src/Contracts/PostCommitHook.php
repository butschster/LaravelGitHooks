<?php

namespace Butschster\GitHooks\Contracts;

use Butschster\GitHooks\Git\Log;
use Closure;

interface PostCommitHook extends Hook
{
    /**
     * @param Log $log
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Log $log, Closure $next);
}
