<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;

class HttpJoinSession extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        if (isset($this->request['401']) && !Disciple::isLoggedIn()) {
            $request = arch\Request::factory('account/login');
            $request->query->rf = $this->request->encode();
            return $this->http->redirect($request);
        }

        $key = $this->data->fetchForAction(
            'axis://session/Stub',
            hex2bin($this->request['key'])
        );

        /*
        if ($key['date']->lt('-1 minute')) {
            throw Exceptional::Forbidden([
                'message' => 'Old stub',
                'http' => 403
            ]);
        }
        */

        if (!$key['sessionId']) {
            $key['sessionId'] = $this->user->session->descriptor->id;
            $key->save();
        }

        return $this->http->defaultRedirect();
    }
}
