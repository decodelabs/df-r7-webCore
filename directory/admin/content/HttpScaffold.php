<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content;

use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const TITLE = 'Published content';
    public const ICON = 'content';

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('./elements/', 'Elements')
                ->setId('elements')
                ->setDescription('Put together reusable blocks of content to use in your pages')
                ->setIcon('element')
                ->setWeight(20)
        );
    }
}
