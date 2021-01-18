<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Groups';
    const ICON = 'group';
    const ADAPTER = 'axis://user/Group';
    const DEFAULT_SECTION = 'users';
    const IS_SHARED = true;

    const SECTIONS = [
        'details',
        'users' => 'user'
    ];

    const LIST_FIELDS = [
        'name', 'signifier', 'roles', 'users'
    ];

    const CONFIRM_DELETE = true;


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('users')
            ->countRelation('roles');
    }

    public function deleteRecord(opal\record\IRecord $group, array $flags=[])
    {
        $group->delete();
        $this->user->instigateGlobalKeyringRegeneration();
        return $this;
    }

    // Components
    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'roles' => $this->html->link('../roles/', $this->_('View roles'))
            ->setIcon('role')
            ->setDisposition('transitive')
            ->addAccessLock('axis://user/Role');
    }


    // Secions
    public function renderDetailsSectionBody($group)
    {
        return $this->apex->scaffold('../roles/')
            ->renderRecordList(
                $group->roles->select(),
                ['actions' => false]
            );
    }

    public function renderUsersSectionBody($group)
    {
        return $this->apex->scaffold('../clients/')
            ->renderRecordList($group->users->select());
    }

    // Fields
    public function defineSignifierField($list, $mode)
    {
        $list->addField('signifier', function ($group) {
            if (!$group['signifier']) {
                return null;
            }
            return Html::{'samp'}($group['signifier']);
        });
    }

    public function defineRolesField($list, $mode)
    {
        if ($mode == 'list') {
            return false;
        }

        $list->addField('roles', function ($group) {
            return Html::uList($group->roles->select()->orderBy('name'), function ($role) {
                return $this->apex->component('../roles/RoleLink', $role);
            });
        });
    }
}
