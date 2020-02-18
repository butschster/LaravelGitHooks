<?php

namespace Butschster\GitHooks\Contracts;

use Butschster\GitHooks\Git\CommitMessage;
use Closure;

interface MessageHook extends Hook
{
    /**
     * @param CommitMessage $message
     * @param Closure $next
     */
    public function handle(CommitMessage $message, Closure $next);
}
