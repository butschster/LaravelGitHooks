<?php

return [
    // The pre-commit hook is run first, before you even type in a commit message. It’s used to inspect the snapshot
    // that’s about to be committed, to see if you’ve forgotten something, to make sure tests run, or to examine
    // whatever you need to inspect in the code. Exiting non-zero from this hook aborts the commit, although you
    // can bypass it with git commit --no-verify. You can do things like check for code style (run lint or something
    // equivalent), check for trailing whitespace (the default hook does exactly this), or check for appropriate
    // documentation on new methods.
    'pre-commit' => [

    ],

    // The prepare-commit-msg hook is run before the commit message editor is fired up but after the default message
    // is created. It lets you edit the default message before the commit author sees it. This hook takes a few
    // parameters: the path to the file that holds the commit message so far, the type of commit, and the commit
    // SHA-1 if this is an amended commit. This hook generally isn’t useful for normal commits; rather, it’s good for
    // commits where the default message is auto-generated, such as templated commit messages, merge commits, squashed
    // commits, and amended commits. You may use it in conjunction with a commit template to programmatically insert
    // information.
    'prepare-commit-msg' => [

    ],

    // The commit-msg hook takes one parameter, which again is the path to a temporary file that contains the commit
    // message written by the developer. If this script exits non-zero, Git aborts the commit process, so you can use
    // it to validate your project state or commit message before allowing a commit to go through. In the last section
    // of this chapter, we’ll demonstrate using this hook to check that your commit message is conformant to a required
    // pattern.
    'commit-msg' => [

    ],

    // After the entire commit process is completed, the post-commit hook runs. It doesn’t take any parameters,
    // but you can easily get the last commit by running git log -1 HEAD. Generally, this script is used for
    // notification or something similar.
    'post-commit' => [

    ],

    // The pre-rebase hook runs before you rebase anything and can halt the process by exiting non-zero. You can use
    // this hook to disallow rebasing any commits that have already been pushed. The example pre-rebase hook that Git
    // installs does this, although it makes some assumptions that may not match with your workflow.
    'pre-rebase' => [

    ],

    // The post-rewrite hook is run by commands that replace commits, such as git commit --amend and git rebase
    // (though not by git filter-branch). Its single argument is which command triggered the rewrite, and it receives
    // a list of rewrites on stdin. This hook has many of the same uses as the post-checkout and post-merge hooks.
    'post-rewrite' => [

    ],

    // After you run a successful git checkout, the post-checkout hook runs; you can use it to set up your working
    // directory properly for your project environment. This may mean moving in large binary files that you don’t want
    // source controlled, auto-generating documentation, or something along those lines.
    'post-checkout' => [

    ],

    // The post-merge hook runs after a successful merge command. You can use it to restore data in the working tree
    // that Git can’t track, such as permissions data. This hook can likewise validate the presence of files external
    // to Git control that you may want copied in when the working tree changes.
    'post-merge' => [

    ],

    // The pre-push hook runs during git push, after the remote refs have been updated but before any objects have
    // been transferred. It receives the name and location of the remote as parameters, and a list of to-be-updated
    // refs through stdin. You can use it to validate a set of ref updates before a push occurs (a non-zero exit code
    // will abort the push).
    'pre-push' => [

    ]
];
