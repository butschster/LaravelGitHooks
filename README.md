# Git hooks manager for Laravel application

Laravel Git Hooks manager is a beautiful tools for Laravel applications. 
It provides a simple and flexible API to manage git hooks, edit commit messages

[![Build Status](https://travis-ci.org/butschster/LaravelGitHooks.svg?branch=master)](https://travis-ci.org/butschster/LaravelGitHooks) [![Latest Stable Version](https://poser.pugx.org/butschster/laravel-git-hooks/v/stable)](https://packagist.org/packages/butschster/laravel-git-hooks) [![Total Downloads](https://poser.pugx.org/butschster/laravel-git-hooks/downloads)](https://packagist.org/packages/butschster/laravel-git-hooks) [![License](https://poser.pugx.org/butschster/laravel-git-hooks/license)](https://packagist.org/packages/butschster/meta-tags)


## Features

- Manage git hooks
- Edit commit messages
- Using custom hooks
- Well documented
- Well tested

## Requirements

- Laravel 5.6 to 6.x
- PHP 7.2 and above

## Installation

From the command line run

    composer require --dev butschster/laravel-git-hooks

Register git hooks by running artisan command

    php artisan git:register-hooks

**That's it!**

## Configuration

Publish the config file.

    php artisan vendor:publish --provider="Butschster\GitHooks\Providers\GitHooksServiceProvider" --tag=config

## Hooks

### pre-commit

The `pre-commit` hook is run first, before you even type in a commit message. It’s used to inspect the snapshot that’s about to be committed, to see if you’ve forgotten something, to make sure tests run, or to examine whatever you need to inspect in the code. Exiting non-zero from this hook aborts the commit, although you can bypass it with `git commit --no-verify`. You can do things like check for code style (run lint or something equivalent), check for trailing whitespace (the default hook does exactly this), or check for appropriate documentation on new methods.

```php
// config/git_hooks.php
return [
    ...
    'pre-commit' => [
        \App\Console\GitHooks\MyPreCommitHook::class,
    ],
    ...
];

// App/Console/GitHooks/MyPreCommitHook.php

namespace \App\Console\GitHooks;

use \Butschster\GitHooks\Git\ChangedFiles;
use Closure;

class MyPreCommitHook implements \Butschster\GitHooks\Contracts\PreCommitHook
{

    public function getName() : string
    {
        return '...';
    }

    public function handle(ChangedFiles $files, Closure $next)
    {
        // do something

        // If you want to cancel commit, you have to throw an exception.

        // run next hook
        return $next($files);
    }
}
```

### prepare-commit-msg

The `prepare-commit-msg` hook is run before the commit message editor is fired up but after the default message is created. It lets you edit the default message before the commit author sees it. This hook takes a few parameters: the path to the file that holds the commit message so far, the type of commit, and the commit SHA-1 if this is an amended commit. This hook generally isn’t useful for normal commits; rather, it’s good for commits where the default message is auto-generated, such as templated commit messages, merge commits, squashed commits, and amended commits. You may use it in conjunction with a commit template to programmatically insert information.

```php
// config/git_hooks.php
return [
    ...
    'prepare-commit-msg' => [
        \App\Console\GitHooks\MyFirstPrepareCommitHook::class,
    ],
    ...
];

// App/Console/GitHooks/MyFirstPrepareCommitHook.php

namespace \App\Console\GitHooks;

use Butschster\GitHooks\Git\CommitMessage;
use Closure;

class MyFirstPrepareCommitHook implements \Butschster\GitHooks\Contracts\MessageHook
{
    public function getName() : string
    {
        return '...';
    }

    public function handle(CommitMessage $message, Closure $next)
    {
        // do something

        $currentMessage = $message->getMessage();

        // You can update commit message text
        $message->setMessage(str_replace('issue', 'fixed', $currentMessage));

        // If you want to cancel commit, you have to throw an exception.

        // run next hook
        return $next($message);
    }
}
```

### commit-msg

The `commit-msg` hook takes one parameter, which again is the path to a temporary file that contains the commit message written by the developer. If this script exits non-zero, Git aborts the commit process, so you can use it to validate your project state or commit message before allowing a commit to go through.

```php
// config/git_hooks.php
return [
    ...
    'commit-msg' => [
        \App\Console\GitHooks\MyFirstCommitMessageHook::class,
    ],
    ...
];

// App/Console/GitHooks/MyFirstCommitMessageHook.php

namespace \App\Console\GitHooks;

use Butschster\GitHooks\Git\CommitMessage;
use Closure;

class MyFirstCommitMessageHook implements \Butschster\GitHooks\Contracts\MessageHook
{
    public function getName() : string
    {
        return '...';
    }

    public function handle(CommitMessage $message, Closure $next)
    {
        // do something

        $currentMessage = $message->getMessage();

        // You can update commit message text
        $message->setMessage(str_replace('issue', 'fixed', $currentMessage));

        // If you want to cancel commit, you have to throw an exception.

        // run next hook
        return $next($message);
    }
}
```

### post-commit

After the entire commit process is completed, the post-commit hook runs. It doesn’t take any parameters, but you can easily get the last commit by running git log -1 HEAD. Generally, this script is used for notification or something similar.


```php
// config/git_hooks.php
return [
    ...
    'post-commit' => [
        \App\Console\GitHooks\NotifyAboutNewCommit::class,
    ],
    ...
];

// App/Console/GitHooks/NotifyAboutNewCommit.php

namespace \App\Console\GitHooks;

use Butschster\GitHooks\Git\Log;
use Closure;

class NotifyAboutNewCommit implements \Butschster\GitHooks\Contracts\PostCommitHook
{
    public function getName() : string
    {
        return '...';
    }

    public function handle(Log $log, Closure $next)
    {
        $hash = $log->getHash();
        $author = $log->getAuthor();
        $date = $log->getDate();
        $message = $log->getMessage();

        // do something

        // If you want to cancel, you have to throw an exception.

        // run next hook
        return $next($log);
    }
}
```
