<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\lists;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const TITLE = 'Mailing lists';
    const ICON = 'list';
    const KEY_NAME = 'source';
    const NAME_FIELD = 'id';

    const LIST_FIELDS = [
        'id', 'adapter', 'primaryListId'
    ];


// Record data
    protected function generateRecordAdapter() {
        $manager = flow\Manager::getInstance();
        $sources = $manager->getListSources();
        $data = [];

        foreach($sources as $source) {
            $data[] = [
                'id' => $source->getId(),
                'adapter' => $source->getAdapter()->getName(),
                'primaryListId' => $source->getPrimaryListId(),
                '@source' => $source
            ];
        }

        return new opal\native\QuerySourceAdapter('sources', $data, 'id');
    }


    public function deleteRecord(opal\record\IRecord $record, array $flags=[]) {
        $id = $record['id'];
        $config = flow\mail\Config::getInstance();
        unset($config->values->listSources->{$id});
        $config->save();
        flow\mailingList\Cache::getInstance()->remove('source:default');
        return $this;
    }


// Fields

}