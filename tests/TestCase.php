<?php

namespace Butschster\GitHooks\Tests;

use Butschster\GitHooks\Contracts\CommitMessageStorage;
use Butschster\GitHooks\Contracts\Configurator;
use Butschster\GitHooks\Tests\Concerns\WithTmpFiles;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Mockery as m;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    protected $tearDownCallbacks = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();

        $this->callTearDownCallbacks();
    }

    protected function setUp(): void
    {
        $this->setUpTraits();

        parent::setUp();
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[WithTmpFiles::class])) {
            $this->registerTmpTrait();
        }

        return $uses;
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function tearDownCallback(callable $callback)
    {
        $this->tearDownCallbacks[] = $callback;
    }

    /**
     * Execute the application's pre-destruction callbacks.
     *
     * @return void
     */
    protected function callTearDownCallbacks()
    {
        foreach ($this->tearDownCallbacks as $callback) {
            $callback();
        }
    }

    /**
     * @return Repository|m\MockInterface
     */
    protected function makeConfig()
    {
        return m::mock(Repository::class);
    }

    /**
     * @return Application|m\MockInterface
     */
    protected function makeApplication()
    {
        return m::mock(Application::class);
    }

    /**
     * @return Configurator|m\LegacyMockInterface|m\MockInterface
     */
    protected function makeConfigurator()
    {
        return m::mock(Configurator::class);
    }

    /**
     * @return CommitMessageStorage|m\LegacyMockInterface|m\MockInterface
     */
    protected function makeCommitMessageStorage()
    {
        return m::mock(CommitMessageStorage::class);
    }
}
