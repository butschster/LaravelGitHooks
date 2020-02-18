<?php

namespace Butschster\GitHooks\Console\Commands\concerns;

use Butschster\GitHooks\Contracts\Hook;
use Butschster\GitHooks\HooksPipeline;
use Illuminate\Pipeline\Pipeline;

trait WithPipeline
{
    /**
     * @param array $hooks
     *
     * @return Pipeline
     */
    public function makePipeline(array $hooks): Pipeline
    {
        return (new HooksPipeline($this->getLaravel()))
            ->through($hooks)
            ->withCallback($this->showInfoAboutHook())
            ->withErrorCallback($this->showInfoAboutHookException());
    }

    protected function showInfoAboutHook()
    {
        return function (Hook $hook) {
            $this->info(sprintf('Hook: %s...', $hook->getName()));
        };
    }

    protected function showInfoAboutHookException()
    {
        return function (Hook $hook, $e) {
            $this->error(sprintf('Failed hook: %s', $hook->getName()));
            $this->error(sprintf('Reason: %s', $e->getMessage()));
        };
    }
}
