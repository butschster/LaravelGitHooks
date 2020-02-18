<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PrepareCommitMessage;
use Butschster\GitHooks\Contracts\MessageHook;
use Butschster\GitHooks\Exceptions\HookFailException;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Console\OutputStyle;
use Mockery as m;
use Symfony\Component\Process\Process;

class PrepareCommitMessageTest extends TestCase
{
    function test_get_command_name()
    {
        $config = $this->makeConfig();
        $commitMessageStorage = $this->makeCommitMessageStorage();

        $command = new PrepareCommitMessage($config, $commitMessageStorage);

        $this->assertEquals('git:prepare-commit-msg', $command->getName());
    }

    function test_requires_file_argument()
    {
        $config = $this->makeConfig();
        $commitMessageStorage = $this->makeCommitMessageStorage();

        $command = new PrepareCommitMessage($config, $commitMessageStorage);

        $this->assertTrue($command->getDefinition()->hasArgument('file'));
    }

    function test_a_message_should_be_send_through_the_hook_pipes()
    {
        $app = $this->makeApplication();
        $app->shouldReceive('basePath')->andReturnUsing(function ($path = null) {
            return $path;
        });

        $app->shouldReceive('make')->andReturnUsing(function ($class) {
            return new $class;
        });

        $config = new Repository([
            'git_hooks' => [
                'prepare-commit-msg' => [
                    PrepareCommitMessageTestHook1::class,
                    PrepareCommitMessageTestHook2::class
                ]
            ]
        ]);

        $commitMessageStorage = $this->makeCommitMessageStorage();

        $commitMessageStorage
            ->shouldReceive('get')
            ->once()
            ->andReturn('Test commit');

        $commitMessageStorage
            ->shouldReceive('update')
            ->once()
            ->with('tmp/COMMIT_MESSAGE', 'Test commit hook1 hook2');

        $command = new PrepareCommitMessage($config, $commitMessageStorage);

        $input = m::mock(\Symfony\Component\Console\Input\InputInterface::class);
        $input->shouldReceive('getArgument')
            ->twice()
            ->with('file')
            ->andReturn('tmp/COMMIT_MESSAGE');

        $command->setLaravel($app);
        $command->setInput($input);

        $output = m::mock(OutputStyle::class);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 1...</info>', 32);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 2...</info>', 32);

        $command->setOutput($output);

        $process = m::mock(Process::class);
        $process->shouldReceive('getOutput')->once()->andReturn('AM src/ChangedFiles.php');

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $this->assertEquals(0, $command->handle($gitCommand));
    }

    function test_failed_hook()
    {
        $app = $this->makeApplication();
        $app->shouldReceive('basePath')->andReturnUsing(function ($path = null) {
            return $path;
        });

        $app->shouldReceive('make')->andReturnUsing(function ($class) {
            return new $class;
        });

        $config = new Repository([
            'git_hooks' => [
                'prepare-commit-msg' => [
                    PrepareCommitMessageTestHook3::class,
                    PrepareCommitMessageTestHook2::class
                ]
            ]
        ]);

        $commitMessageStorage = $this->makeCommitMessageStorage();

        $commitMessageStorage
            ->shouldReceive('get')
            ->once()
            ->andReturn('Test commit');

        $commitMessageStorage
            ->shouldNotReceive('update');

        $command = new PrepareCommitMessage($config, $commitMessageStorage);

        $input = m::mock(\Symfony\Component\Console\Input\InputInterface::class);
        $input->shouldReceive('getArgument')
            ->once()
            ->with('file')
            ->andReturn('tmp/COMMIT_MESSAGE');

        $command->setLaravel($app);
        $command->setInput($input);

        $output = m::mock(OutputStyle::class);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 3...</info>', 32);

        $output->shouldNotReceive('writeln')
            ->with('<info>Hook: hook 2...</info>', 32);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<error>Failed hook: hook 3</error>', 32);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<error>Reason: Failed hook</error>', 32);

        $command->setOutput($output);

        $process = m::mock(Process::class);
        $process->shouldReceive('getOutput')->once()->andReturn('AM src/ChangedFiles.php');

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $this->assertEquals(1, $command->handle($gitCommand));
    }
}

class PrepareCommitMessageTestHook1 implements MessageHook
{
    /**
     * @inheritDoc
     */
    public function handle(\Butschster\GitHooks\Git\CommitMessage $message, Closure $next)
    {
        $message->setMessage($message->getMessage().' hook1');

        return $next($message);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'hook 1';
    }
}

class PrepareCommitMessageTestHook2 implements MessageHook
{

    /**
     * @inheritDoc
     */
    public function handle(\Butschster\GitHooks\Git\CommitMessage $message, Closure $next)
    {
        $message->setMessage($message->getMessage().' hook2');

        return $next($message);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'hook 2';
    }
}


class PrepareCommitMessageTestHook3 implements MessageHook
{

    /**
     * @inheritDoc
     */
    public function handle(\Butschster\GitHooks\Git\CommitMessage $message, Closure $next)
    {
        $message->setMessage($message->getMessage().' hook2');

        throw new Exception('Failed hook');

        return $next($message);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'hook 3';
    }
}
