<?php

namespace Butschster\GitHooks\Console\Commands;

class CommitMessage extends PrepareCommitMessage
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:commit-msg {file}';

    /**
     * The console command description.
     */
    protected $description = 'Run hook commit-msg';

    /**
     * @var string
     */
    protected $hook = 'commit-msg';
}
