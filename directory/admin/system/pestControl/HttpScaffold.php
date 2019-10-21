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

class HttpScaffold extends arch\scaffold\AreaMenu
{
    const TITLE = 'Pest control';
    const ICON = 'bug';

    protected $_router;

    public function generateIndexMenu($entryList)
    {
        $criticalErrorCount = $this->data->pestControl->error->countAll();
        $notFoundCount = $this->data->pestControl->miss->countAll();
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
                ->setDescription('View requests that users are making to files and nodes that don\'t exist')
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

    public function addIndexOperativeLinks($menu, $bar)
    {
        $menu->addLinks(
            $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
                ->setIcon('delete')
        );
    }


    // Helpers
    public function defineRequestField($list, $mode)
    {
        $list->addField('request', function ($item, $context) use ($mode) {
            if (!$request = $item['request']) {
                return;
            }
            $context->getCellTag()->setStyle('word-break', 'break-all');

            switch ($item['mode']) {
                case 'Http':
                    $router = $this->_getRouter();

                    if (preg_match('/^http(s)?:/', $request)) {
                        $url = new df\link\http\Url($request);
                    } else {
                        $baseUrl = (string)$router->getBaseUrl();
                        $url = new df\link\http\Url($baseUrl.$request);
                    }

                    $output = $router->urlToRequest($url);
                    unset(
                        $output->query->rf, $output->query->rt,
                        $url->query->rf, $url->query->rt
                    );

                    $title = $output->toReadableString();
                    $output = (string)$request;
                    break;

                default:
                    $output = (string)$request;
                    $title = $url = null;
                    break;
            }

            if ($mode == 'list') {
                $output = $this->format->shorten($output, 60, true);
            }

            $output = $this->html('code', $output);

            if ($mode == 'list' && $title !== null) {
                $output->setTitle($title);
            }

            if ($item['mode'] == 'Http') {
                $output = $this->html->link($url, $output)
                    ->setIcon('link')
                    ->setDisposition('transitive')
                    ->setTarget('_blank');
            }

            yield $output;
        });
    }

    protected function _getRouter()
    {
        if (!$this->_router) {
            $this->_router = core\app\runner\http\Router::getInstance();
        }

        return $this->_router;
    }
}
