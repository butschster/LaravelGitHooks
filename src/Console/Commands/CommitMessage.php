<?php

namespace Butschster\GitHooks\Console\Commands;

use Butschster\GitHooks\Console\Commands\concerns\WithCommitMessage;
use Butschster\GitHooks\Contracts\CommitMessageStorage;
use Butschster\GitHooks\Contracts\HookCommand;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Console\Command;

class CommitMessage extends Command implements HookCommand
{
    use WithCommitMessage;

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
     * @param Repository $config
     * @param CommitMessageStorage $messageStorage
     */
    public function __construct(Repository $config, CommitMessageStorage $messageStorage)
    {
        parent::__construct();

        $this->config = $config;
        $this->messageStorage = $messageStorage;
    }

    /**
     * @inheritDoc
     */
    public function getHook(): string
    {
        return 'commit-msg';
    }
}
