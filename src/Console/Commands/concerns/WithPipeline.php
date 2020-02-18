<?php

namespace Butschster\GitHooks\Console\Commands\concerns;

use Butschster\GitHooks\Contracts\Hook;
use Butschster\GitHooks\HooksPipeline;
use Closure;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

trait WithPipeline
{
    /**
     * @inheritDoc
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * @return Pipeline
     */
    protected function makePipeline(): Pipeline
    {
        $pipeline = new HooksPipeline(
            $this->getLaravel(),
            $this->getConfig(),
            $this->getHook()
        );

        return $pipeline
            ->through($this->getRegisteredHooks())
            ->withCallback($this->showInfoAboutHook())
            ->withErrorCallback($this->showInfoAboutHookException());
    }

    protected function showInfoAboutHook(): Closure
    {
        return function (Hook $hook) {
            $this->info(sprintf('Hook: %s...', $hook->getName()));
        };
    }

    protected function showInfoAboutHookException(): Closure
    {
        return function (Hook $hook, Exception $e) {
            $this->error(sprintf('Failed hook: %s', $hook->getName()));
            $this->error(sprintf('Reason: %s', $e->getMessage()));
        };
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredHooks(): array
    {
        $hooks = new Collection((array) $this->config->get('git_hooks.'.$this->getHook()));

        return $hooks->map(function($hook, $i) {
            if (is_int($i)) {
                return $hook;
            }

            return $i;
        })->all();
    }
}
