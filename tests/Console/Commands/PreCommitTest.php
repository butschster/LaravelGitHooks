<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PreCommit;
use Butschster\GitHooks\Contracts\PreCommitHook;
use Butschster\GitHooks\Git\ChangedFiles;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Illuminate\Config\Repository;
use Mockery as m;
use Symfony\Component\Process\Process;

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

        $config = new Repository([
            'git_hooks' => [
                'pre-commit' => [
                    $hook1,
                    $hook2
                ]
            ]
        ]);

        $app = $this->makeApplication();
        $command = new PreCommit($config);
        $command->setLaravel($app);

        $process = m::mock(Process::class);
        $process->shouldReceive('getOutput')->once()->andReturn('AM src/ChangedFiles.php');

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $command->handle($gitCommand);

        $this->assertTrue(true);
    }
}
