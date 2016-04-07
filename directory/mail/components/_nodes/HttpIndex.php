<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\components\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute() {
        return $this->apex->newWidgetView(function($view) {
            $mails = $this->_getTemplateList();

            yield $this->html->collectionList($mails)
                ->addField('name', function($mail, $context) {
                    $name = $context->getKey();

                    if(!$mail) {
                        return $this->html('span.error', $this->html->icon('mail', $name));
                    }

                    return $this->html->link(
                            '~mail/components/view?path='.$name,
                            $name
                        )
                        ->setIcon('theme')
                        ->setDisposition('informative');
                })
                ->addField('description', function($mail) {
                    if($mail) {
                        return $mail->getDescription();
                    }
                })
                ->addField('actions', function($mail, $context) {
                    if($mail) {
                        return $this->html->link(
                                $this->uri('~mail/components/preview?path='.$context->key, true),
                                $this->_('Send preview')
                            )
                            ->setIcon('mail')
                            ->setDisposition('positive');
                    }
                });
        });
    }

    protected function _getTemplateList() {
        $list = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/mail', 'php', function($path) {
            return false !== strpos($path, '_components');
        });

        $mails = [];

        foreach($list as $name => $filePath) {
            $parts = explode('_components/', substr($name, 0, -4), 2);
            $path = array_shift($parts);
            $name = array_shift($parts);

            if(false !== strpos($name, '/')) {
                $path .= '#/';
            }

            $name = $path.$name;
            $path = '~mail/'.$name;

            try {
                $component = $this->apex->component($path);
            } catch(\Exception $e) {
                $mails[$name] = null;
                continue;
            }

            if(!$component instanceof arch\IMailComponent) {
                continue;
            }

            $mails[$name] = $component;
        }

        ksort($mails);
        return $mails;
    }
}