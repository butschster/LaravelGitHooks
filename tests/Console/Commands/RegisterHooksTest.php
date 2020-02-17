<?php

namespace Butschster\GitHooks\Tests\Console\Commands;

use Butschster\GitHooks\Console\Commands\RegisterHooks;
use Butschster\GitHooks\Tests\TestCase;
use Illuminate\Console\OutputStyle;
use Mockery as m;

class RegisterHooksTest extends TestCase
{
    function test_get_command_name()
    {
        $command = new RegisterHooks();

        $this->assertEquals('git:register-hooks', $command->getName());
    }

    function test_run_configurator()
    {
        $configurator = $this->makeConfigurator();

        $configurator
            ->shouldReceive('run')
            ->once();

        $command = new RegisterHooks();

        $command->setOutput($output = m::mock(OutputStyle::class));

        $output->shouldReceive('writeLn')
            ->once()
            ->with('<info>Git hooks have been successfully created</info>', 32);

        $command->handle($configurator);

        $this->assertTrue(true);
    }
}
