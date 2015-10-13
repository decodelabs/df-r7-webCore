<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {

    const DIRECTORY_TITLE = 'Pest control';
    const DIRECTORY_ICON = 'bug';

    protected $_router;

    public function generateIndexMenu($entryList) {
        $criticalErrorCount = $this->data->pestControl->errorLog->countAll();
        $notFoundCount = $this->data->pestControl->missLog->countAll();
        $accessErrorCount = $this->data->pestControl->accessLog->countAll();

        $entryList->addEntries(
            $entryList->newLink('./errors/', 'Critical errors')
                ->setId('errors')
                ->setDescription('Get detailed information on critical errors encountered by users')
                ->setIcon('error')
                ->setNote($this->format->counterNote($criticalErrorCount))
                ->setWeight(10),

            $entryList->newLink('./misses/', '404 errors')
                ->setId('misses')
                ->setDescription('View requests that users are making to files and actions that don\'t exist')
                ->setIcon('brokenLink')
                ->setNote($this->format->counterNote($notFoundCount))
                ->setWeight(20),

            $entryList->newLink('./access/', 'Access errors')
                ->setId('access')
                ->setDescription('See who is trying to access things they are not supposed to')
                ->setIcon('lock')
                ->setNote($this->format->counterNote($accessErrorCount))
                ->setWeight(30)
        );
    }

    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
                ->setIcon('delete')
        );
    }


// Helpers
    public function defineRequestField($list, $mode) {
        $list->addField('request', function($item, $context) use($mode) {
            if(!$request = $item['request']) return;
            $context->getCellTag()->setStyle('word-break', 'break-all');

            switch($item['mode']) {
                case 'Http':
                    $router = $this->_getRouter();
                    $request = new arch\Request($request);
                    $output = $router->routeIn($request);
                    unset($output->query->rf, $output->query->rt);
                    $title = $output->toReadableString();
                    $url = $router->requestToUrl($request);
                    $output = (string)$request;
                    break;

                default:
                    $output = (string)$request;
                    $title = null;
                    break;
            }

            if($mode == 'list') {
                $output = $this->format->shorten($output, 60, true);
            }

            $output = $this->html('code', $output);

            if($mode == 'list' && $title !== null) {
                $output->setTitle($title);
            }

            if($item['mode'] == 'Http') {
                $output = $this->html->link($url, $output)
                    ->setIcon('link')
                    ->setDisposition('transitive')
                    ->setTarget('_blank');
            }

            return $output;
        });
    }

    protected function _getRouter() {
        if(!$this->_router) {
            $this->_router = core\application\http\Router::getInstance();
        }

        return $this->_router;
    }
}