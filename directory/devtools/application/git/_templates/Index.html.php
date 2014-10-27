<?php
echo $this->import->component('~devtools/application/git/IndexHeaderBar');

echo $this->html->collectionList($this['packageList'])
    ->setErrorMessage($this->_('No packages could be found'))

    // Name
    ->addField('location', function($package, $context) {
        if(!$package['instance']) {
            $context->getRowTag()->addClass('disabled');
        }

        return \df\core\io\Util::stripLocationFromFilePath($package['path']);
    })

    // Priority
    ->addField('priority', function($package) {
        if($package['name'] == 'app') {
            return 'top';
        } else if($package['instance']) {
            return $package['instance']->priority;
        }
    })

    // Changes
    ->addField('changes', function($package, $context) {
        if(!$package['repo']) {
            return null;
        }

        $status = $package['repo']->getCommitStatus();

        $hasChanges = false;
        $output = [];

        if($status->hasTracked()) {
            $output[] = $this->html->icon('edit', $status->countTracked());
            $hasChanges = true;
        }

        if($status->hasUntracked()) {
            $output[] = $this->html->icon('plus', $status->countUntracked());
            $hasChanges = true;
        }

        if($commits = $status->countUnpushedCommits()) {
            $output[] = $this->html->icon('upload', $commits);
            $hasChanges = true;
        }

        if($commits = $status->countUnpulledCommits()) {
            $output[] = $this->html->icon('download', $commits);
        }

        $context->setStore('hasChanges', $hasChanges);

        return $output;
    })

    // Commits
    ->addField('commits', function($package) {
        if(!$package['repo']) {
            return null;
        }

        return $this->html->icon('star', $package['repo']->countCommits('master'));
    })

    // Actions
    ->addField('Actions', function($package, $context) {
        if($package['repo']) {
            return [
                $this->html->link(
                        $this->uri('~devtools/application/git/refresh?package='.$package['name'], true),
                        $this->_('Refresh')
                    )
                    ->setIcon('refresh'),

                $this->html->link(
                        $this->uri('~devtools/application/git/update?package='.$package['name'], true),
                        $this->_('Update')
                    )
                    ->setIcon('download')
                    ->setDisposition('operative')
            ];
        }
    }); 