<?php

namespace Butschster\GitHooks\Console\Commands;

use Butschster\GitHooks\Console\Commands\concerns\WithCommitMessage;
use Butschster\GitHooks\Contracts\CommitMessageStorage;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class PrepareCommitMessage extends Command
{
    use WithCommitMessage;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:prepare-commit-msg {file}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run hook prepare-commit-msg';

    /**
     * @var string
     */
    protected $hook = 'prepare-commit-msg';

    /**
     * @param Repository $config
     * @param CommitMessageStorage $messageStorage
     */
    public function __construct(Repository $config, CommitMessageStorage $messageStorage)
    {
        parent::__construct();

        $this->config = $config;
        $this->messageStorage = $messageStorage;
    }
}
