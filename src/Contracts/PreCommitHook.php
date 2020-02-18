<?php

namespace Butschster\GitHooks\Contracts;

use Butschster\GitHooks\Git\ChangedFiles;
use Closure;

interface PreCommitHook extends Hook
{
    /**
     * @param ChangedFiles $files
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(ChangedFiles $files, Closure $next);
}
