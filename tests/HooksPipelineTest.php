<?php

namespace Butschster\GitHooks\Tests;

use Butschster\GitHooks\Contracts\Hook;
use Butschster\GitHooks\HooksPipeline;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;

class HooksPipelineTest extends TestCase
{
    function test_pass_data_through_pipes()
    {
        $container = new Container();

        $hooks = [
            HooksPipelineTestHook::class,
            HooksPipelineTestWithArgsHook::class
        ];

        $config = new Repository([
            'git_hooks' => [
                'pre-commit' => [
                    HooksPipelineTestHook::class,
                    HooksPipelineTestWithArgsHook::class => [
                        'param' => 'Hook 2'
                    ]
                ]
            ]
        ]);

        $pipeline = new HooksPipeline($container, $config, 'pre-commit');

        $message = $pipeline->through($hooks)
            ->send('message')
            ->thenReturn();

        $this->assertEquals('message Hook 1 Hook 2', $message);
    }
}

class HooksPipelineTestHook implements Hook
{
    /**
     * Get hook name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Hook 1';
    }

    public function handle(string $message, $next)
    {
        $message .= ' '. $this->getName();

        return $next($message);
    }
}

class HooksPipelineTestWithArgsHook implements Hook
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get hook name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Hook 2';
    }

    public function handle(string $message, $next)
    {
        $message .= ' '. $this->config['param'];

        return $next($message);
    }
}