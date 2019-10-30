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

use GuzzleHttp\Client as HttpClient;

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
                    $httpClient = new HttpClient();
                    $response = $httpClient->get('http://api6.ipify.org');
                    $ip = link\Ip::factory((string)$response->getBody());
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
