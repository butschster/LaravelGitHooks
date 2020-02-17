<?php

namespace Butschster\GitHooks\Contracts;

interface Configurator
{
    /**
     * Register git hooks
     */
    public function run(): void;
}
