<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PostCommit;
use Butschster\GitHooks\Contracts\PostCommitHook;
use Butschster\GitHooks\Git\GetLasCommitFromLog;
use Butschster\GitHooks\Git\Log;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Mockery as m;

class PostCommitTest extends TestCase
{
    function test_get_command_name()
    {
        $config = $this->makeConfig();
        $command = new PostCommit($config);

        $this->assertEquals('git:post-commit', $command->getName());
    }

    function test_a_message_should_be_send_through_the_hook_pipes()
    {
        $config = $this->makeConfig();

        $command = new PostCommit($config);

        $hook1 = m::mock(PostCommitHook::class);
        $hook1->shouldReceive('handle')
            ->once()
            ->andReturnUsing(function (Log $log, Closure $closure) {

                $this->assertEquals('bfdc6c406626223bf3cbb65b8d269f7b65ca0570', $log->getHash());

                return $closure($log);
            });

        $hook2 = m::mock(PostCommitHook::class);
        $hook2->shouldReceive('handle')
            ->once()
            ->andReturnUsing(function (Log $log, Closure $closure) {

                $this->assertEquals('bfdc6c406626223bf3cbb65b8d269f7b65ca0570', $log->getHash());

                return $closure($log);
            });

        $config->shouldReceive('get')
            ->with('git_hooks.post-commit')
            ->once()
            ->andReturn([
                $hook1,
                $hook2
            ]);

        $gitCommand = m::mock(GetLasCommitFromLog::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn([
            'commit bfdc6c406626223bf3cbb65b8d269f7b65ca0570',
            'Author: Pavel Buchnev <butschster@gmail.com>',
            'Date:   Tue Feb 18 12:01:15 2020 +0300',
            '',
            '    Added PreCommit hooks.',
            '',
            '    Added docs for `pre-commit`, `prepare-commit-msg`, `commit-msg`',
            '',
            '    fixed #2',
        ]);

        $command->handle($gitCommand);

        $this->assertTrue(true);
    }
}