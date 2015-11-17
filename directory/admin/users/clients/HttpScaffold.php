<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\user;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {

    const DIRECTORY_TITLE = 'Users';
    const DIRECTORY_ICON = 'user';
    const RECORD_ADAPTER = 'axis://user/Client';
    const RECORD_KEY_NAME = 'user';
    const RECORD_NAME_FIELD = 'fullName';

    protected $_sections = [
        'details',
        'invites' => 'mail',
        'authentication' => 'lock'
    ];

    protected $_recordListFields = [
        'fullName', 'email', 'status', 'groups',
        'country', 'joinDate', 'loginDate'
    ];

    protected $_recordDetailsFields = [
        'fullName', 'nickName', 'email', 'status',
        'deactivation', 'country', 'language',
        'timezone', 'joinDate', 'loginDate', 'groups'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query->countRelation('groups');
    }

    protected function countSectionItems($record) {
        return [
            'invites' => $this->data->user->invite->select()
                ->where('owner', '=', $record['id'])
                ->count(),
            'authentication' => $record->authDomains->countAll()
        ];
    }


// Sections
    public function renderDetailsSectionBody($client) {
        return $this->html->panelSet()
            ->addPanel('details', 50, parent::renderDetailsSectionBody($client))
            ->addPanel('avatar', 50, [
                $this->html->image(
                    $this->avatar->getAvatarUrl($client['id'], 400),
                    'Avatar'
                )
            ]);
    }

    public function renderInvitesSectionBody($client) {
        return $this->apex->scaffold('../invites/')
            ->renderRecordList(
                $this->data->user->invite->select()
                    ->where('owner', '=', $client['id']),
                ['owner' => false]
            );
    }

    public function renderAuthenticationSectionBody($client) {
        $authenticationList = $client->authDomains->fetch()
            ->orderBy('adapter ASC');

        return $this->html->collectionList($authenticationList)
            ->setErrorMessage($this->_('There are no authentication entries to display'))

            // Adapter
            ->addField('adapter')

            // Identity
            ->addField('identity')

            // Bind date
            ->addField('bindDate', function($auth) {
                return $this->html->date($auth['bindDate']);
            })

            // Login date
            ->addField('loginDate', $this->_('Last login'), function($auth) {
                if($auth['loginDate']) {
                    return $this->html->timeSince($auth['loginDate']);
                }
            })

            // Actions
            ->addField('actions', function($auth) {
                if($auth['adapter'] == 'Local') {
                    return $this->html->link(
                            $this->uri('./change-password?user='.$auth['#user'], true),
                            $this->_('Change password')
                        )
                        ->setIcon('edit')
                        ->setDisposition('operative');
                }
            });
    }



// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('../settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../groups/', $this->_('Groups'))
                ->setIcon('group')
                ->setDisposition('transitive'),

            $this->html->link('../roles/', $this->_('Roles'))
                ->setIcon('role')
                ->setDisposition('transitive'),

            $this->html->link('../invites/', $this->_('Invites'))
                ->setIcon('mail')
                ->setDisposition('transitive')
        );
    }

    public function addDetailsSectionSubOperativeLinks($menu, $bar) {
        if($this->_record->hasLocalAuth()) {
            $menu->addLinks(
                // Change password
                $this->html->link(
                        $this->uri('./change-password?user='.$this->_record['id'], true),
                        $this->_('Change password')
                    )
                    ->setIcon('edit')
                    ->setDisposition('operative')
            );
        }
    }

    public function addAuthenticationSectionSubOperativeLinks($menu, $bar) {
        $this->addDetailsSectionSubOperativeLinks($menu, $bar);
    }


// Fields
    public function defineEmailField($list, $mode) {
        if($mode == 'details') {
            $list->addField('email', function($client) {
                $emailList = $this->data->user->emailVerify->fetchEmailList($client);

                return $this->html->bulletList($emailList, function($verify) use($client) {
                    $output = $this->html->mailLink($verify['email'])
                        ->setIcon($verify['verifyDate'] ? 'tick' : 'cross')
                        ->addClass($verify['email'] == $client['email'] ? null : 'disabled');

                    return $output;
                });
            });
        } else {
            $list->addField('email', function($client) {
                return $this->html->mailLink($client['email']);
            });
        }
    }

    public function defineStatusField($list, $mode) {
        $list->addField('status', function($client, $context) use($mode) {
            if($client['status'] == user\IState::DEACTIVATED) {
                if($mode == 'list') {
                    $context->getRowTag()->addClass('disabled');
                }

                $context->getCellTag()->addClass('negative');
            } else if($client['status'] == user\IState::PENDING) {
                $context->getCellTag()->addClass('warning');
            }

            return $this->user->client->stateIdToName($client['status']);
        });
    }

    public function defineDeactivationField($list, $mode) {
        if($mode != 'details') {
            return;
        }

        $list->addField('deactivation', function($client, $context) {
            if($client['status'] != user\IState::DEACTIVATED) {
                return $context->skipRow();
            }

            $deactivation = $this->context->data->user->clientDeactivation->fetch()
                ->where('user', '=', $client)
                ->toRow();

            if(!$deactivation) {
                return $context->skipRow();
            }

            $output = [
                $this->html('p', $deactivation['reason'])
            ];

            if($deactivation['comments']) {
                $output[] = $this->html('div', $this->html->plainText($deactivation['comments']));
            }

            return $output;
        });
    }

    public function defineLoginDateField($list, $mode) {
        $list->addField('loginDate', $mode == 'list' ? $this->_('Login') : $this->_('Last login'), function($client) {
            if($client['loginDate']) {
                return $this->html->timeSince($client['loginDate']);
            }
        });
    }

    public function defineCountryField($list, $mode) {
        $list->addField('country', function($client) use($mode) {
            $output = $this->i18n->countries->getName($client['country']);

            if($mode == 'list') {
                $output = $this->html('abbr', $client['country'], [
                    'title' => $output
                ]);
            }

            return $output;
        });
    }

    public function defineLanguageField($list) {
        $list->addField('language', function($client) {
            return $this->i18n->languages->getName($client['language']);
        });
    }

    public function defineTimezoneField($list) {
        $list->addField('timezone', function($client) {
            return $this->i18n->timezones->getName($client['timezone']);
        });
    }

    public function defineGroupsField($list, $mode) {
        $list->addField('groups', function($client) use($mode) {
            if($mode == 'list') {
                return $client['groups'];
            }

            $groupList = $client->groups->fetch()->orderBy('Name');

            return $this->html->bulletList($groupList, function($client) {
                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }
}