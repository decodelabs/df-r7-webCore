<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $container = $this->aura->getWidgetContainer();
        $container->push($this->directory->getComponent('IndexHeaderBar', '~admin/system/'));
        $container->addBlockMenu('directory://~admin/system/Index');

        return $container;
    }

    public function importLegacyAction() {
        $count = 0;

        foreach($this->data->error->log->fetch() as $log) {
            switch($log['code']) {
                case 401:
                case 403:
                    $count++;
                    $this->data->log->accessError->newRecord([
                            'date' => $log['date'],
                            'mode' => $log['mode'],
                            'code' => $log['code'],
                            'request' => $log['request'],
                            'message' => $log['message'],
                            'user' => $log->getRawId('user'),
                            'isProduction' => $log['isProduction']
                        ])
                        ->save();

                    break;

                case 404:
                    $count++;
                    $this->data->log->notFound->newRecord([
                            'date' => $log['date'],
                            'mode' => $log['mode'],
                            'request' => $log['request'],
                            'message' => $log['message'],
                            'user' => $log->getRawId('user'),
                            'isProduction' => $log['isProduction']
                        ])
                        ->save();

                    break;

                case 500:
                    $count++;
                    $this->data->log->criticalError->newRecord([
                            'date' => $log['date'],
                            'mode' => $log['mode'],
                            'request' => $log['request'],
                            'message' => $log['message'],
                            'user' => $log->getRawId('user'),
                            'isProduction' => $log['isProduction']
                        ])
                        ->save();

                    break;
            }
        }

        core\dump($count);
    }
}