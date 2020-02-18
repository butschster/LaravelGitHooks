<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PreCommit;
use Butschster\GitHooks\Contracts\PreCommitHook;
use Butschster\GitHooks\Git\ChangedFiles;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Mockery as m;

class PreCommitTest extends TestCase
{
    function test_get_command_name()
    {
        $config = $this->makeConfig();
        $command = new PreCommit($config);

        $this->assertEquals('git:pre-commit', $command->getName());
    }

    function test_a_message_should_be_send_through_the_hook_pipes()
    {
        $config = $this->makeConfig();

        $command = new PreCommit($config);

        $hook1 = m::mock(PreCommitHook::class);
        $hook1->shouldReceive('handle')
            ->once()
            ->andReturnUsing(function (ChangedFiles $files, Closure $closure) {
                $this->assertEquals('AM src/ChangedFiles.php', (string) $files->getFiles()->first());
                return $closure($files);
            });

        $hook2 = m::mock(PreCommitHook::class);
        $hook2->shouldReceive('handle')
            ->once()
            ->andReturnUsing(function (ChangedFiles $files, Closure $closure) {
                $this->assertEquals('AM src/ChangedFiles.php', (string) $files->getFiles()->first());
                return $closure($files);
            });

        $config->shouldReceive('get')
            ->with('git_hooks.pre-commit')
            ->once()
            ->andReturn([
                $hook1,
                $hook2
            ]);

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn([
            'AM src/ChangedFiles.php'
        ]);

        $command->handle($gitCommand);

        $this->assertTrue(true);
    }
}
