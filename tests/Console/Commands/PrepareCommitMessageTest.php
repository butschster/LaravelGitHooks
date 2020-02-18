<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\PrepareCommitMessage;
use Butschster\GitHooks\Contracts\MessageHook;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Mockery as m;

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

        $config = $this->makeConfig();
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

        $config->shouldReceive('get')
            ->with('git_hooks.prepare-commit-msg')
            ->once()
            ->andReturn([
                PrepareCommitMessageTestHook1::class,
                PrepareCommitMessageTestHook2::class
        ]);

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $gitCommand->shouldReceive('exec')->once()->andReturn([
            'AM src/ChangedFiles.php'
        ]);

        $command->handle($gitCommand);

        $this->assertTrue(true);
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
}
