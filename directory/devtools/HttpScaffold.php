<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools;

use DecodeLabs\Dictum;
use DecodeLabs\Tagged as Html;

use df;
use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const TITLE = 'Devtools';
    public const ICON = 'debug';
    public const HEADER_BAR = false;

    protected function renderIntro($view)
    {
        $view->content->addAttributeList()
            ->addField('COMPILE_TIMESTAMP', function () {
                return Html::$time->dateTime(df\COMPILE_TIMESTAMP);
            })
            ->addField('COMPILE_BUILD_ID', function () {
                return Html::{'?code'}(df\COMPILE_BUILD_ID);
            })
            ->addField('COMPILE_ROOT_PATH', function () {
                return Html::{'?code'}(df\COMPILE_ROOT_PATH);
            })
            ->addField('COMPILE_ENV_MODE', function () {
                return Dictum::name(df\COMPILE_ENV_MODE);
            });
    }

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('~devtools/models/', 'Data models')
                ->setId('models')
                ->setDescription('Manage and update database schemas and data')
                ->setIcon('database')
                ->setWeight(20),
            $entryList->newLink('~devtools/processes/', 'Process manager')
                ->setId('processManager')
                ->setDescription('Queue and schedule tasks, launch daemons and view logs of previously run processes')
                ->setIcon('task')
                ->setWeight(30),
            $entryList->newLink('~devtools/cache/', 'Cache control')
                ->setId('cache')
                ->setDescription('Refresh, clear and view stats for most cache structures your site employs')
                ->setIcon('toolkit')
                ->setWeight(40)
        );
    }
}
