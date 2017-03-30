<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\previews\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute() {
        return $this->apex->newWidgetView(function($view) {
            $mails = $this->_getMailList();

            yield $this->html->collectionList($mails)
                ->addField('name', function($mail, $context) {
                    if(!$mail) {
                        return $this->html('span.error', $this->html->icon('mail', $context->key));
                    }

                    $url = $this->uri->directoryRequest('~mail/previews/view');
                    $url->query->path = $mail['path'];

                    return $this->html->link($url, $mail['name'])
                        ->setIcon('theme')
                        ->setDisposition('informative');
                })
                ->addField('description', function($mail) {
                    if($mail) {
                        return $mail['description'];
                    }
                })
                ->addField('actions', function($mail, $context) {
                    if($mail) {
                        $url = $this->uri->directoryRequest('~mail/previews/preview');
                        $url->query->path = $mail['path'];

                        return $this->html->link($this->uri($url, true), $this->_('Send preview'))
                            ->setIcon('mail')
                            ->setDisposition('positive');
                    }
                });
        });
    }

    protected function _getMailList() {
        $list = df\Launchpad::$loader->lookupFileListRecursive('apex/directory', ['php'], function($path) {
            return false !== strpos($path, '_mail');
        });

        $mails = [];

        foreach($list as $name => $filePath) {
            $parts = explode('_mail/', substr($name, 0, -4), 2);
            $path = array_shift($parts);
            $name = array_shift($parts);

            if(false !== strpos($name, '/')) {
                $path .= '#/';
            }


            $name = $path.$name;
            $path = '~'.$name;

            try {
                $mail = $this->comms->prepareMail($path);
            } catch(\Throwable $e) {
                $mails[$path] = null;
                continue;
            }

            $name = $path;

            if(substr($name, 0, 7) == '~front/') {
                $name = substr($name, 7);
            }

            $mails[$path] = [
                'name' => $name,
                'path' => $path,
                'description' => $mail->getDescription()
            ];
        }

        ksort($mails);
        return $mails;
    }
}