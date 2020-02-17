<?php

namespace Butschster\GitHooks\Console\Commands;

use Butschster\GitHooks\Contracts\CommitMessageStorage;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Pipeline\Pipeline;

class PrepareCommitMessage extends Command
{
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
     * @var Repository
     */
    protected $config;

    /**
     * @var CommitMessageStorage
     */
    protected $messageStorage;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');

        $message = $this->messageStorage->get(
            $this->getLaravel()->basePath($file)
        );

        $this->sendMessageThroughHooks($message);
    }

    /**
     * Get the git message path (By default .git/COMMIT_MESSAGE)
     * @return string
     */
    private function getMessagePath(): string
    {
        $file = $this->argument('file');

        return $this->getLaravel()->basePath($file);
    }

    /**
     * Send the given message from .git/COMMIT_MESSAGE through the pipes
     *
     * @param string $message
     *
     * @return string
     */
    protected function sendMessageThroughHooks(string $message)
    {
        $hooks = $this->getHooks();

        return (new Pipeline($this->getLaravel()))
            ->send($message)
            ->through($hooks)
            ->then($this->saveMessage());
    }

    /**
     * Store prepared message
     *
     * @return Closure
     */
    protected function saveMessage()
    {
        return function (string $message) {
            $this->messageStorage->update(
                $this->getMessagePath(),
                $message
            );
        };
    }

    /**
     * @return array
     */
    protected function getHooks(): array
    {
        return (array) $this->config->get('git_hooks.' . $this->hook);
    }
}
