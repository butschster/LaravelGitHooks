<?php

namespace Butschster\GitHooks\Tests\Git;

use Butschster\GitHooks\Git\ChangedFiles;
use Butschster\GitHooks\Git\CommitMessage;
use Butschster\GitHooks\Tests\TestCase;

class CommitMessageTest extends TestCase
{
    function test_gettter_setter_message()
    {
        $commitMessage = new CommitMessage('Test message', new ChangedFiles([]));

        $this->assertEquals('Test message', $commitMessage->getMessage());

        $commitMessage->setMessage('New message');

        $this->assertEquals('New message', $commitMessage->getMessage());
    }
}