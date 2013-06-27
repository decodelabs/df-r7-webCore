<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class SectionLinks_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $context = $this->getContext();

        $userId = $this->getContext()->request->query['user'];
        $authenticationCount = $context->data->user->auth->select()
            ->where('user', '=', $userId)
            ->count();

        $entryList->addEntries(
            $entryList->newLink('~admin/users/details?user='.$userId, 'Details')
                ->setId('details')
                ->setIcon('details')
                ->setWeight(1)
                ->setDisposition('informative'),

            $entryList->newLink('~admin/users/authentication?user='.$userId, 'Authentication')
                ->setId('authentication')
                ->setIcon('user')
                ->setNote($context->format->counterNote($authenticationCount))
                ->setWeight(20)
                ->setDisposition('informative')
        );
    }
}