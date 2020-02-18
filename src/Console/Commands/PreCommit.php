<?php

namespace Butschster\GitHooks\Console\Commands;

use Butschster\GitHooks\Git\ChangedFiles;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Pipeline\Pipeline;

class PreCommit extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:pre-commit';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run hook pre-commit';

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
     * @param GetListOfChangedFiles $command
     * @return mixed
     */
    public function handle(GetListOfChangedFiles $command)
    {
        $changedFiles = $command->exec();

        $this->sendChangedFilesThroughHooks(
            new ChangedFiles($changedFiles)
        );
    }

    /**
     * Send the changed files through the pipes
     *
     * @param ChangedFiles $files
     */
    protected function sendChangedFilesThroughHooks(ChangedFiles $files): void
    {
        $hooks = $this->getHooks();

        (new Pipeline($this->getLaravel()))
            ->send($files)
            ->through($hooks)
            ->then($this->doNothing());
    }

    /**
     * @return Closure
     */
    protected function doNothing()
    {
        return function () {

        };
    }

    /**
     * @return array
     */
    protected function getHooks(): array
    {
        return (array) $this->config->get('git_hooks.pre-commit');
    }
}
