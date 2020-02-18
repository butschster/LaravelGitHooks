<?php

namespace Butschster\GitHooks\Git;

use Carbon\Carbon;

class Log
{
    /**
     * @var array
     */
    protected $lines;

    /**
     * @var false|string
     */
    private $hash;

    /**
     * @var false|string
     */
    private $author;

    /**
     * @var Carbon
     */
    private $date;

    /**
     * @var array
     */
    private $merge = [];

    /**
     * @var string
     */
    private $message = '';

    /**
     * @param array $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;

        $this->parse($lines);
    }

    /**
     * Parse current log into variables
     *
     * @param array $lines
     */
    private function parse(array $lines): void
    {
        foreach ($lines as $key => $line) {
            if (strpos($line, 'commit') === 0) {
                $this->hash = substr($line, strlen('commit') + 1);
            } else if (strpos($line, 'Author') === 0) {
                $this->author = substr($line, strlen('Author:') + 1);
            } else if (strpos($line, 'Date') === 0) {
                $this->date = Carbon::parse(substr($line, strlen('Date:') + 3));
            } else if (strpos($line, 'Merge') === 0) {
                $merge = substr($line, strlen('Merge:') + 1);
                $this->merge = explode(' ', $merge);
            } else if (!empty($line)) {
                $this->message .= substr($line, 4) . "\n";
            }
        }
    }

    /**
     * Get commit hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Get author
     *
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Get commit date
     *
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * Get merge information
     *
     * @return array
     */
    public function getMerge(): array
    {
        return $this->merge;
    }

    /**
     * Get commit message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getHash();
    }
}
