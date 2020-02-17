# Git hooks manager for Laravel application

Laravel Git Hooks manager is a beautiful tools for Laravel applications. 
It provides a simple and flexible API to manage git hooks, edit commit messages

[![Build Status](https://travis-ci.org/butschster/LaravelGitHooks.svg?branch=master)](https://travis-ci.org/butschster/LaravelGitHooks) [Latest Stable Version](https://packagist.org/packages/butschster/laravel-git-hooks/v/stable)](https://packagist.org/packages/butschster/laravel-git-hooks) [![Total Downloads](https://packagist.org/packages/butschster/laravel-git-hooks/downloads)](https://packagist.org/packages/butschster/laravel-git-hooks) [![License](https://poser.pugx.org/butschster/laravel-git-hooks/license)](https://packagist.org/packages/butschster/laravel-git-hooks)

## Features

- Manage git hooks
- Edit commit messages
- Using custom hooks
- Well documented
- Well tested

## Requirements

- Laravel 5.6 to 6.x
- PHP 7.2 and above

## Installation and Configuration

From the command line run

    composer require --dev butschster/laravel-git-hooks

Publish the config file.

    php artisan vendor:publish --provider="Butschster\GitHooks\Providers\GitHooksServiceProvider" --tag=config
    
Add yous hooks into config file

    // config/git_hooks.php
    
    return [
        ...
        'commit-msg' => [
            \App\Console\GitHooks\MyFirstHook::class,
        ],
        ...
    ];

That's it!


