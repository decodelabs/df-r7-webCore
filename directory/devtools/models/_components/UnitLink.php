<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class UnitLink extends arch\component\RecordLink
{
    protected $icon = 'unit';
    protected $maxLength = 35;

    // Url
    protected function getRecordUri(string $id)
    {
        return '~devtools/models/unit-details?unit='.$id;
    }

    protected function getRecordId(): string
    {
        return (string)$this->record->getId();
    }

    protected function getRecordName()
    {
        return $this->record->getId();
    }
}
