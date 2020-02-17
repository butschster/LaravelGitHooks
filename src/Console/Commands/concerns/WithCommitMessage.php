<?php

namespace Butschster\GitHooks\Console\Commands\concerns;

use Butschster\GitHooks\Git\ChangedFiles;
use Butschster\GitHooks\Git\CommitMessage;
use Butschster\GitHooks\Contracts\CommitMessageStorage;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Pipeline\Pipeline;

trait WithCommitMessage
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var CommitMessageStorage
     */
    protected $messageStorage;

    /**
     * Execute the console command.
     *
     * @param GetListOfChangedFiles $command
     * @return mixed
     */
    public function handle(GetListOfChangedFiles $command)
    {
        $file = $this->argument('file');

        $message = $this->messageStorage->get(
            $this->getLaravel()->basePath($file)
        );

        $changedFiles = $command->exec();

        $this->sendMessageThroughHooks(
            new CommitMessage(
                $message,
                new ChangedFiles($changedFiles)
            )
        );
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
     * @param CommitMessage $message
     */
    protected function sendMessageThroughHooks(CommitMessage $message): void
    {
        $hooks = $this->getHooks();

        (new Pipeline($this->getLaravel()))
            ->send($message)
            ->through($hooks)
            ->then($this->storeMessage());
    }

    /**
     * Store prepared message
     *
     * @return Closure
     */
    protected function storeMessage()
    {
        return function (CommitMessage $message) {
            $this->messageStorage->update(
                $this->getMessagePath(),
                (string) $message
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