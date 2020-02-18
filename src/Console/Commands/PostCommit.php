<?php

namespace Butschster\GitHooks\Console\Commands;

use Butschster\GitHooks\Console\Commands\concerns\WithPipeline;
use Butschster\GitHooks\Git\GetLasCommitFromLog;
use Butschster\GitHooks\Git\Log;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class PostCommit extends Command
{
    use WithPipeline;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:post-commit';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run hook post-commit';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @param GetLasCommitFromLog $command
     * @return mixed
     */
    public function handle(GetLasCommitFromLog $command)
    {
        $this->sendLogCommitThroughHooks(
            new Log(
                $command->exec()->getOutput()
            )
        );
    }

    /**
     * Send the log commit through the pipes
     *
     * @param Log $log
     */
    protected function sendLogCommitThroughHooks(Log $log): void
    {
        $hooks = $this->getHooks();

        $this->makePipeline($hooks)
            ->send($log)
            ->thenReturn();
    }

    /**
     * @return array
     */
    protected function getHooks(): array
    {
        return (array) $this->config->get('git_hooks.post-commit');
    }
}
