<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const TITLE = 'Devtools';
    const ICON = 'debug';
    const HEADER_BAR = false;

    protected function renderIntro($view)
    {
        $view->content->addAttributeList()
            ->addField('COMPILE_TIMESTAMP', function () {
                return $this->html->dateTime(df\COMPILE_TIMESTAMP);
            })
            ->addField('COMPILE_BUILD_ID', function () {
                return Html::{'?code'}(df\COMPILE_BUILD_ID);
            })
            ->addField('COMPILE_ROOT_PATH', function () {
                return Html::{'?code'}(df\COMPILE_ROOT_PATH);
            })
            ->addField('COMPILE_ENV_MODE', function () {
                return $this->format->name(df\COMPILE_ENV_MODE);
            });
    }

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('~devtools/users/', 'User setup')
                ->setId('users')
                ->setDescription('Configure root user, authentication adapters and session settings')
                ->setIcon('user')
                ->setWeight(10),

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

            $entryList->newLink('~devtools/theme/', 'Theme configuration')
                ->setId('theme')
                ->setDescription('Change settings and define layouts for available site themes')
                ->setIcon('theme')
                ->setWeight(40),

            $entryList->newLink('~devtools/application/', 'Application utilities')
                ->setId('application')
                ->setDescription('View stats, generate testing and production versions etc.')
                ->setIcon('stats')
                ->setWeight(50),

            $entryList->newLink('~devtools/cache/', 'Cache control')
                ->setId('cache')
                ->setDescription('Refresh, clear and view stats for most cache structures your site employs')
                ->setIcon('toolkit')
                ->setWeight(60),

            $entryList->newLink('~devtools/media/', 'Media tools')
                ->setId('media')
                ->setDescription('Control where and how your media is stored and served')
                ->setIcon('folder')
                ->setWeight(70)
        );
    }
}
