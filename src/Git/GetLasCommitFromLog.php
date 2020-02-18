<?php

namespace Butschster\GitHooks\Git;

use Butschster\GitHooks\Contracts\GitCommand;

class GetLasCommitFromLog implements GitCommand
{
    /**
     * @inheritDoc
     */
    public function exec(): array
    {
        exec('git log -1 HEAD', $output, $status);

        return $output;
    }
}
