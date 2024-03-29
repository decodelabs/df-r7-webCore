<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system;

use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const TITLE = 'System';
    public const ICON = 'controlPanel';

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('./pest-control/', 'Pest control')
                ->setId('pest-control')
                ->setDescription('View logs of critical errors, page misses and acces revocations')
                ->setIcon('bug')
                ->setWeight(10),
            $entryList->newLink('./geo-ip/', 'Geo IP')
                ->setId('geo-ip')
                ->setDescription('Set up IP lookup tools to find out where your users are')
                ->setIcon('location')
                ->setWeight(20)
        );
    }
}
