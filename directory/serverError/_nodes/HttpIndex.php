<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\serverError\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\link;
use df\aura;

use DecodeLabs\Atlas;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;

class HttpIndex extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        if (!Genesis::$environment->isDevelopment()) {
            throw Exceptional::Runtime(
                'Server error generators can only be run in development mode'
            );
        }

        $code = $this->request['error'];

        if (!link\http\response\HeaderCollection::isErrorStatusCode($code)) {
            throw Exceptional::UnexpectedValue(
                'Invalid status code: '.$code
            );
        }

        $xCode = substr($code, 0, 2).'x';

        $paths = [
            '~serverError/'.$code.'.html' => 'serverError/_templates/'.$code.'.html.php',
            '~serverError/'.$xCode.'.html' => 'serverError/_templates/'.$xCode.'.html.php',
            '~front/error/'.$code.'.html' => 'front/error/_templates/'.$code.'.html.php',
            '~front/error/'.$xCode.'.html' => 'front/error/_templates/'.$xCode.'.html.php'
        ];

        $templatePath = null;

        foreach ($paths as $relPath => $path) {
            $path = Genesis::$hub->getApplicationPath().'/directory/'.$path;

            if (file_exists($path)) {
                $templatePath = $relPath;
                break;
            }
        }

        if (!$templatePath) {
            $templatePath = '~front/error/'.$code.'.html';
        }

        try {
            $view = $this->apex->view($templatePath);
        } catch (aura\view\NotFoundException $e) {
            $templatePath = substr($templatePath, 0, -6).'x.html';

            try {
                $view = $this->apex->view($templatePath);
            } catch (aura\view\NotFoundException $f) {
                try {
                    $view = $this->apex->view('Default.html');
                } catch (aura\view\NotFoundException $f) {
                    throw $e;
                }
            }
        }

        $view
            ->setSlots([
                'code' => $code,
                'message' => $message = link\http\response\HeaderCollection::statusCodeToMessage($code)
            ])
            ->setTheme('serverError')
            ->setLayout('Default')
            ->setTitle($message);

        Atlas::createFile(
            Genesis::$hub->getApplicationPath().'/serverError/'.$code.'.html',
            $view->render()
        );

        return $view;
    }
}
