# Git hooks manager for Laravel application

Laravel Git Hooks manager is a beautiful tools for Laravel applications. 
It provides a simple and flexible API to manage git hooks, edit commit messages

## Features

- Manage git hooks
- Edit commit messages
- Using custom hooks
- Well documented
- Well tested

## Requirements

- Laravel 5.6 to 6.x
- PHP 7.1 and above

## Installation and Configuration

From the command line run

    composer require --dev butschster/laravel-git-hooks

Publish the config file.

    php artisan vendor:publish --provider="Butschster\GitHooks\Providers\GitHooksServiceProvider" --tag=config
    
Add yous hooks into config file

    ```php
    // config/git_hooks.php
    
    return [
        ...
        'commit-msg' => [
            \App\Console\GitHooks\MyFirstHook::class,
        ],
        ...
    ];
    ```

That's it!


