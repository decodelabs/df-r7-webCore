<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\previews\_nodes;

use DecodeLabs\R7\Legacy;

use DecodeLabs\Tagged as Html;
use df\arch;

class HttpView extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $mail = $this->comms->preparePreviewMail($this->request['path']);
        $html = $mail->getBodyHtml();

        if ($html !== null) {
            if (false === stripos($html, '<body')) {
                $view = $mail->context->apex->newWidgetView();
                $view->setTheme(false);
                $view->content->push(Html::raw($html));
                $view->shouldUseLayout(false);
                $view->setTitle($mail->getSubject());

                return $view;
            } else {
                return Legacy::$http->stringResponse($html, 'text/html');
            }
        }

        return Legacy::$http->stringResponse($mail->getBodyText(), 'text/plain');
    }
}
