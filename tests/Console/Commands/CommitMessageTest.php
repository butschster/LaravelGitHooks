<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\CommitMessage;
use Butschster\GitHooks\Contracts\MessageHook;
use Butschster\GitHooks\Git\GetListOfChangedFiles;
use Butschster\GitHooks\Tests\TestCase;
use Closure;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Console\OutputStyle;
use Mockery as m;
use Symfony\Component\Process\Process;

class CommitMessageTest extends TestCase
{
    function test_get_command_name()
    {
        $config = $this->makeConfig();
        $commitMessageStorage = $this->makeCommitMessageStorage();

        $command = new CommitMessage($config, $commitMessageStorage);

        $this->assertEquals('git:commit-msg', $command->getName());
    }

    function test_requires_file_argument()
    {
        $config = $this->makeConfig();
        $commitMessageStorage = $this->makeCommitMessageStorage();

        $command = new CommitMessage($config, $commitMessageStorage);

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
                'commit-msg' => [
                    CommitMessageTestHook1::class,
                    CommitMessageTestHook2::class
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

        $command = new CommitMessage($config, $commitMessageStorage);

        $command->setLaravel($app);

        $input = m::mock(\Symfony\Component\Console\Input\InputInterface::class);
        $input->shouldReceive('getArgument')
            ->with('file')
            ->andReturn('tmp/COMMIT_MESSAGE');

        $output = m::mock(OutputStyle::class);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 1...</info>', 32);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 2...</info>', 32);

        $command->setOutput($output);
        $command->setInput($input);

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $process = m::mock(Process::class);
        $process->shouldReceive('getOutput')->once()->andReturn('AM src/ChangedFiles.php');
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $this->assertEquals(0, $command->handle($gitCommand));

        $this->assertTrue(true);
    }

    function test_pass_hook_config_into_hook_object()
    {
        $app = $this->makeApplication();
        $app->shouldReceive('make')->andReturnUsing(function ($class, array $parameters = []) {
            return new $class(...array_values($parameters));
        });

        $app->shouldReceive('basePath')->andReturnUsing(function ($path = null) {
            return $path;
        });

        $config = new Repository([
            'git_hooks' => [
                'commit-msg' => [
                    CommitMessageTestHook4::class => [
                        'param1' => 'hello',
                        'param2' => 'world'
                    ],
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
            ->with('tmp/COMMIT_MESSAGE', 'Test commit hello world');

        $command = new CommitMessage($config, $commitMessageStorage);

        $command->setLaravel($app);

        $input = m::mock(\Symfony\Component\Console\Input\InputInterface::class);
        $input->shouldReceive('getArgument')
            ->with('file')
            ->andReturn('tmp/COMMIT_MESSAGE');

        $output = m::mock(OutputStyle::class);

        $output->shouldReceive('writeln')
            ->once()
            ->with('<info>Hook: hook 4...</info>', 32);

        $command->setOutput($output);
        $command->setInput($input);

        $gitCommand = m::mock(GetListOfChangedFiles::class);
        $process = m::mock(Process::class);
        $process->shouldReceive('getOutput')->once()->andReturn('AM src/ChangedFiles.php');
        $gitCommand->shouldReceive('exec')->once()->andReturn($process);

        $this->assertEquals(0, $command->handle($gitCommand));

        $this->assertTrue(true);
    }
}

class CommitMessageTestHook1 implements MessageHook
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

class CommitMessageTestHook2 implements MessageHook
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

class CommitMessageTestHook3 implements MessageHook
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

class CommitMessageTestHook4 implements MessageHook
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function handle(\Butschster\GitHooks\Git\CommitMessage $message, Closure $next)
    {
        $message->setMessage($message->getMessage().' '.$this->config['param1'].' '. $this->config['param2']);

        return $next($message);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'hook 4';
    }
}

