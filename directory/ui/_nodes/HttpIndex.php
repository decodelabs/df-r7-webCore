<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->app->isProduction()) {
            throw core\Error::{'EForbidden'}([
                'message' => 'Dev mode only',
                'http' => 403
            ]);
        }

        return $this->apex->newWidgetView(function($view) {
            $files = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/ui/_templates', ['php']);

            yield $this->html->collectionList($files)
                ->addField('name', function($filePath, $context) {
                    $name = substr($context->getKey(), 0, -4);
                    return $this->html->link(
                            '~ui/view?path='.$name,
                            $name
                        )
                        ->setIcon('file')
                        ->setDisposition('informative');
                })
                ->addField('created', function($filePath) {
                    return $this->format->date(@filectime($filePath));
                })
                ->addField('modified', function($filePath) {
                    return $this->format->timeFromNow(@filemtime($filePath));
                });
        });
    }
}
