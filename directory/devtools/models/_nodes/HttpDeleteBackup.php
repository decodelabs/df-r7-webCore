<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;

use DecodeLabs\Tagged as Html;
use DecodeLabs\Exceptional;
use DecodeLabs\Atlas;

class HttpDeleteBackup extends arch\node\DeleteForm
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'backup';

    protected $_file;

    protected function init()
    {
        $fileName = basename($this->request['backup']);

        if (!preg_match('/^axis\-[0-9]+\.tar$/i', $fileName)) {
            throw Exceptional::Forbidden([
                'message' => 'Not an axis backup file',
                'http' => 403
            ]);
        }

        $this->_file = $this->app->getSharedDataPath().'/backup/'.$fileName;

        if (!is_file($this->_file)) {
            throw Exceptional::NotFound([
                'message' => 'Backup not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId()
    {
        return basename($this->_file);
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList(basename($this->_file))
            ->addField('name', function ($backup) {
                return $backup;
            })
            ->addField('created', function ($backup) {
                return Html::$time->since(\df\core\time\Date::fromCompressedString(substr($backup, 5, -4), 'UTC'));
            });
    }

    protected function apply()
    {
        Atlas::$fs->deleteFile($this->_file);
    }
}
