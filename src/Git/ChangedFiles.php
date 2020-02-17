<?php

namespace Butschster\GitHooks\Git;

use Illuminate\Support\Collection;

class ChangedFiles
{
    /**
     * @var Collection
     */
    protected $files;

    /**
     * @param array $gitStatus
     */
    public function __construct(array $gitStatus)
    {
        $this->files = collect($gitStatus)
            ->map(function (string $line) {
                return new ChangedFile($line);
            });
    }

    /**
     * Get all files with changes
     *
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Get added to commit files
     *
     * @return Collection
     */
    public function getAddedToCommit(): Collection
    {
        return $this->files->filter(function (ChangedFile $file) {
            return $file->isInCommit();
        });
    }

    /**
     * @return Collection
     */
    public function getDeleted(): Collection
    {
        return $this->files->filter(function (ChangedFile $file) {
            return $file->isDeleted();
        });
    }

    /**
     * Get untracked files
     *
     * @return Collection
     */
    public function getUntracked(): Collection
    {
        return $this->files->filter(function (ChangedFile $file) {
            return $file->isUntracked();
        });
    }
}