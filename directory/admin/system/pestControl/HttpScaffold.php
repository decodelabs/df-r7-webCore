<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl;

use DecodeLabs\Dictum;
use DecodeLabs\R7\Legacy;

use DecodeLabs\Tagged as Html;
use df;
use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const TITLE = 'Pest control';
    public const ICON = 'bug';

    protected $_router;

    public function generateIndexMenu($entryList)
    {
        $criticalErrorCount = $this->data->pestControl->error->countAll();
        $notFoundCount = $this->data->pestControl->miss->countAll();
        $accessErrorCount = $this->data->pestControl->accessLog->countAll();
        $reportErrorCount = $this->data->pestControl->report->countAll();

        $entryList->addEntries(
            $entryList->newLink('./errors/', 'Critical errors')
                ->setId('errors')
                ->setDescription('Get detailed information on critical errors encountered by users')
                ->setIcon('error')
                ->setNote(Dictum::$number->counter($criticalErrorCount))
                ->setWeight(10),
            $entryList->newLink('./misses/', '404 errors')
                ->setId('misses')
                ->setDescription('View requests that users are making to files and nodes that don\'t exist')
                ->setIcon('brokenLink')
                ->setNote(Dictum::$number->counter($notFoundCount))
                ->setWeight(20),
            $entryList->newLink('./access/', 'Access errors')
                ->setId('access')
                ->setDescription('See who is trying to access things they are not supposed to')
                ->setIcon('lock')
                ->setNote(Dictum::$number->counter($accessErrorCount))
                ->setWeight(30),
            $entryList->newLink('./reports/', 'HTTP reports')
                ->setId('reports')
                ->setDescription('View HTTP client reports for CSP, etc')
                ->setIcon('report')
                ->setNote(Dictum::$number->counter($reportErrorCount))
                ->setWeight(40)
        );
    }

    public function generateIndexOperativeLinks(): iterable
    {
        yield 'purge' => $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
            ->setIcon('delete');
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
                    $router = Legacy::$http->getRouter();

                    if (preg_match('/^http(s)?:/', (string)$request)) {
                        $url = new df\link\http\Url($request);
                    } else {
                        $baseUrl = (string)$router->getBaseUrl();
                        $url = new df\link\http\Url($baseUrl . $request);
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
                $output = Dictum::shorten($output, 60, true);
            }

            $output = Html::{'code'}($output);

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
}
