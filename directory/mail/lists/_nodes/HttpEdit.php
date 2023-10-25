<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\lists\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Config\Mail as MailConfig;

class HttpEdit extends HttpAdd
{
    protected $_id;
    protected $_source;

    protected function initWithSession(): void
    {
        $config = MailConfig::load();
        $sources = $config->getListSources();
        $this->_id = $this->request['source'];

        if (!isset($sources[$this->_id])) {
            throw Exceptional::{'df/flow/mailingList/NotFound'}([
                'message' => 'Source not found',
                'http' => 404
            ]);
        }

        $this->_source = $sources[$this->_id];

        if ($adapter = $this->_source['adapter']) {
            $this->setStore('adapter', $adapter);
            $this->setStore('options', $this->_source->toArray());
        }
    }

    protected function setDefaultValues(): void
    {
        $this->values->import($this->_source);
        $this->values->id = $this->_id;
    }
}
