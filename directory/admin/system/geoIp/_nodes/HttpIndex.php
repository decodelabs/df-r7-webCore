<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\geoIp\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\link;

class HttpIndex extends arch\node\Base
{
    public function executeAsHtml()
    {
        $view = $this->apex->view('Index.html');
        $handler = link\geoIp\Handler::factory();

        $ip = $this->http->getIp();
        $view['isLoopback'] = false;

        if ($ip->isLoopback()) {
            $view['isLoopback'] = true;
            $ip = link\Ip::factory($_SERVER['SERVER_ADDR']);

            if ($ip->isLoopback()) {
                try {
                    $client = new link\http\Client();
                    $response = $client->get('http://ipecho.net/plain');

                    if ($response->isOk()) {
                        $ip = link\Ip::factory($response->getContent());
                    }
                } catch (\Throwable $e) {
                }
            }
        }

        $view['config'] = link\geoIp\Config::getInstance();
        $view['result'] = $handler->lookup($ip);
        $view['adapterList'] = link\geoIp\Handler::getAdapterList();

        return $view;
    }
}
