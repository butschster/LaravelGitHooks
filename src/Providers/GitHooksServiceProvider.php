<?php

namespace Butschster\GitHooks\Providers;

use Butschster\GitHooks\Configurator;
use Butschster\GitHooks\CommitMessageStorage;
use Butschster\GitHooks\Console\Commands;
use Butschster\GitHooks\Contracts;
use Butschster\GitHooks\HookStorage;
use Illuminate\Support\ServiceProvider;

class GitHooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton(Contracts\Configurator::class, function ($app) {
                $hooks = [
                    'pre-commit', 'prepare-commit-msg', 'commit-msg', 'post-commit',
                    'pre-push', 'pre-rebase', 'post-rewrite', 'post-checkout', 'post-merge'
                ];

                $config = $app['config']->get('git_hooks');

                $hooks = array_filter($hooks, function ($hook) use($config) {
                    return !empty($config[$hook]);
                });

                $storage = $app[Contracts\HookStorage::class];

                return new Configurator($app, $storage, $hooks);
            });

            $this->app->bind(Contracts\HookStorage::class, HookStorage::class);
            $this->app->bind(Contracts\CommitMessageStorage::class, CommitMessageStorage::class);

            $this->commands([
                Commands\RegisterHooks::class,
                Commands\CommitMessage::class,
                Commands\PreCommit::class,
                Commands\PrepareCommitMessage::class,
                Commands\PostCommit::class,
                Commands\PrePush::class,
            ]);
        }
    }

    /**
     * Register the package's publishable resources.
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../../config/git_hooks.php' => $this->app->configPath('git_hooks.php'),
        ], 'config');
    }
}
