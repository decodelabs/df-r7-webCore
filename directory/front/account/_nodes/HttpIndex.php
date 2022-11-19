<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df\arch;

class HttpIndex extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    public function execute()
    {
        return $this->apex->view('Index.html', function ($view) {
            $view
                ->setCanonical('account/')
                ->canIndex(false);
        });
    }
}
