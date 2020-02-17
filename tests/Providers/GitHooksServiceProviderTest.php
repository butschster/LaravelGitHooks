<?php

namespace Butschster\GitHooks\Tests\Providers;

use Butschster\GitHooks\Providers\GitHooksServiceProvider;
use Butschster\GitHooks\Tests\TestCase;

class GitHooksServiceProviderTest extends TestCase
{
    function test_config_file_should_be_published()
    {
        $app = $this->makeApplication();

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);
        $app->shouldReceive('configPath')
            ->once()
            ->with('git_hooks.php')
            ->andReturn('config/git_hooks.php');

        $provider = new GitHooksServiceProvider($app);

        $provider->boot();

        $this->assertEquals([
            'config' => [
                '/home/pbuchnev/webserver/LaravelGitHooks/src/Providers/../../config/git_hooks.php' => 'config/git_hooks.php'
            ]
        ], GitHooksServiceProvider::$publishGroups);
    }
}
