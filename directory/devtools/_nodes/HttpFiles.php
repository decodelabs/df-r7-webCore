<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\_nodes;

use df\arch;

class HttpFiles extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        return $this->apex->view('Files.html', function ($view) {
            yield 'files' => get_included_files();
        });
    }
}
