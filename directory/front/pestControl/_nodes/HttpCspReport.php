<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\pestControl\_nodes;

use df\apex;
use df\arch;

use DecodeLabs\R7\Legacy;

class HttpCspReport extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executePost()
    {
        $data = Legacy::$http->getRequest()->getBodyDataString();

        if (empty($data)) {
            return 'no data';
        }

        $data = json_decode($data, true);

        if (!isset($data['csp-report'])) {
            return 'not CSP';
        }

        $data = $data['csp-report'];

        if (($data['source-file'] ?? null) === 'chrome-extension') {
            return 'ignore extensions';
        }

        $this->data->pestControl->report->storeReport(
            'csp', $data
        );

        return 'done';
    }
}
