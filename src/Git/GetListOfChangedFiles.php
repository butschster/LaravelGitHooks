<?php

namespace Butschster\GitHooks\Git;

use Butschster\GitHooks\Contracts\GitCommand;

class GetListOfChangedFiles implements GitCommand
{
    /**
     * @inheritDoc
     */
    public function exec(): array
    {
        exec('git status --short', $output, $status);

        return (array) $output;
    }
}
