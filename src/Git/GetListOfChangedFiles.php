<?php

namespace Butschster\GitHooks\Git;

class GetListOfChangedFiles
{
    /**
     * @return array
     */
    public function exec(): array
    {
        exec('git status --short', $output, $status);

        return (array) $output;
    }
}