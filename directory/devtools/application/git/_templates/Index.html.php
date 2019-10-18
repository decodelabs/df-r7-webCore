<?php
echo $this->apex->component('~devtools/application/git/IndexHeaderBar');

echo $this->html->collectionList($packageList)
    ->setErrorMessage($this->_('No packages could be found'))

    // Name
    ->addField('location', function ($package, $context) {
        if (!$package['instance']) {
            $context->getRowTag()->addClass('disabled');
        }

        return Glitch::normalizePath($package['path']);
    })

    // Priority
    ->addField('priority', function ($package) {
        if ($package['name'] == 'app') {
            return 'top';
        } elseif ($package['instance']) {
            return $package['instance']->priority;
        }
    })

    // Changes
    ->addField('changes', function ($package, $context) {
        if (!$package['repo']) {
            return null;
        }

        $status = $package['repo']->getCommitStatus();
        $hasChanges = false;


        if ($status->hasTracked()) {
            yield $this->html->icon('edit', $status->countTracked());
            $hasChanges = true;
        }

        if ($status->hasUntracked()) {
            yield $this->html->icon('plus', $status->countUntracked());
            $hasChanges = true;
        }

        $context->setStore('hasChanges', $hasChanges);
    })

    // Commits
    ->addField('commits', function ($package) {
        if (!$package['repo']) {
            return null;
        }

        return $this->html->icon('star', $package['repo']->countCommits('master'));
    });
