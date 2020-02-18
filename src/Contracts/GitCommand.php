<?php

namespace Butschster\GitHooks\Contracts;

interface GitCommand
{
    /**
     * Execute command and return output
     *
     * @return array
     */
    public function exec(): array;
}
