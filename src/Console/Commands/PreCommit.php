<?php

namespace Butschster\GitHooks\Console\Commands;

use Illuminate\Console\Command;

class PreCommit extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'git:pre-commit';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run hook pre-commit';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
