<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PostCommit;
use Butschster\GitHooks\Contracts\PostCommitHook;
use Butschster\GitHooks\Git\GetLasCommitFromLog;
use Butschster\GitHooks\Git\Log;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Illuminate\Config\Repository;
use Mockery as m;
use Symfony\Component\Process\Process;

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

        $config = new Repository([
            'git_hooks' => [
                'post-commit' => [
                    $hook1,
                    $hook2
                ]
            ]
        ]);

        $app = $this->makeApplication();
        $command = new PostCommit($config);
        $command->setLaravel($app);

        $gitCommand = m::mock(GetLasCommitFromLog::class);
        $process = m::mock(Process::class);

        $process->shouldReceive('getOutput')
            ->once()
            ->andReturn(<<<EOL
commit bfdc6c406626223bf3cbb65b8d269f7b65ca0570
Author: Pavel Buchnev <butschster@gmail.com>
Date:   Tue Feb 18 12:01:15 2020 +0300

    Added PreCommit hooks.

    Added docs for `pre-commit`, `prepare-commit-msg`, `commit-msg`

    fixed #2
EOL
            );
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $command->handle($gitCommand);

        $this->assertTrue(true);
    }
}
