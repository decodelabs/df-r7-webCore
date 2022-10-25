<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use df\arch;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Tagged as Html;

class HttpRestoreBackup extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_file;

    protected function init(): void
    {
        $fileName = basename($this->request['backup']);

        if (!preg_match('/^axis\-[0-9]+\.tar$/i', $fileName)) {
            throw Exceptional::Forbidden([
                'message' => 'Not an axis backup file',
                'http' => 403
            ]);
        }

        $this->_file = Genesis::$hub->getSharedDataPath().'/backup/'.$fileName;

        if (!is_file($this->_file)) {
            throw Exceptional::NotFound([
                'message' => 'Backup not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return basename($this->_file);
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to restore this backup?');
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

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Restore'))
            ->setIcon('import');
    }

    protected function apply()
    {
        return $this->task->initiateStream('axis/restore-backup?backup='.basename($this->_file));
    }
}
