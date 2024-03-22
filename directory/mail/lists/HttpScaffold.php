<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\lists;

use DecodeLabs\Tagged as Html;
use df\arch;
use df\flow;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const TITLE = 'Mailing lists';
    public const ICON = 'list';
    public const KEY_NAME = 'source';
    public const NAME_FIELD = 'id';

    public const LIST_FIELDS = [
        'id', 'adapter', 'primaryListId', 'updated'
    ];

    public const CAN_DELETE = false;


    // Record data
    protected function generateRecordAdapter()
    {
        $manager = flow\Manager::getInstance();
        $sources = $manager->getListSources();
        $data = [];

        foreach ($sources as $source) {
            $data[] = [
                'id' => $source->getId(),
                'adapter' => $source->getAdapter()->getName(),
                'primaryListId' => $source->getPrimaryListId(),
                'timestamp' => $source->getManifestTimestamp(),
                '@source' => $source
            ];
        }

        return new opal\native\QuerySourceAdapter('sources', $data, 'id');
    }




    // Components
    public function generateIndexSubOperativeLinks(): iterable
    {
        yield 'refresh' => $this->html->link(
            $this->uri('./refresh', true),
            $this->_('Refresh')
        )
            ->setIcon('refresh');
    }

    // Fields
    public function defineUpdatedField($list, $mode)
    {
        $list->addField('updated', function ($list) {
            return Html::$time->since($list['timestamp']);
        });
    }
}
