<?php

namespace Butschster\GitHooks\Tests;

use Butschster\GitHooks\Configurator;
use Butschster\GitHooks\Contracts\HookStorage;
use Illuminate\Contracts\Foundation\Application;
use Mockery as m;

class ConfiguratorTest extends TestCase
{
    function test_files_for_hooks_should_be_created()
    {
        $storage = m::mock(HookStorage::class);
        $app = $this->makeApplication();

        $app->shouldReceive('basePath')->andReturnUsing(function($path = null) {
            return $path;
        });

        $storage->shouldReceive('store')->with('.git/hooks/pre-commit', <<<EOL
#!/bin/sh

php /artisan git:pre-commit $@ >&2

EOL
);

        $storage->shouldReceive('store')->with('.git/hooks/post-commit', <<<EOL
#!/bin/sh

php /artisan git:post-commit $@ >&2

EOL
);

        $configurator = new Configurator($app, $storage, [
            'pre-commit',
            'post-commit',
        ]);

        $configurator->run();

        $this->assertTrue(true);
    }
}
