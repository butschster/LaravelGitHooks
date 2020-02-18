<?php

namespace Butschster\GitHooks\Contracts;

interface Hook
{
    /**
     * Get hook name
     *
     * @return string
     */
    public function getName(): string;
}
