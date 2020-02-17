<?php

namespace Butschster\GitHooks\Console\Commands;

use Illuminate\Console\Command;
use Butschster\GitHooks\Contracts\Configurator;

class RegisterHooks extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:register-hooks';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Register git hooks for application';

    /**
     * Execute the console command.
     *
     * @param Configurator $configurator
     *
     * @return mixed
     */
    public function handle(Configurator $configurator)
    {
        $configurator->run();

        $this->info('Git hooks have been successfully created');
    }
}
