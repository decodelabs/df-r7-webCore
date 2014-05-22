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
    const RECORD_NAME_KEY = 'fullName';

    protected $_sections = [
        'details',
        'authentication' => [
            'icon' => 'lock'
        ]
    ];

    protected $_recordListFields = [
        'fullName' => true,
        'email' => true,
        'status' => true,
        'groups' => true,
        'country' => true,
        'joinDate' => true,
        'loginDate' => true,
        'actions' => true
    ];

    protected $_recordDetailsFields = [
        'fullName' => true,
        'nickName' => true,
        'email' => true,
        'status' => true,
        'deactivation' => true,
        'country' => true,
        'language' => true,
        'timezone' => true,
        'joinDate' => true,
        'loginDate' => true,
        'groups' => true
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query->countRelation('groups');
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->where('id', '=', ltrim($search, '#'))
            ->orWhere('fullName', 'matches', $search)
            ->orWhere('nickName', 'matches', $search)
            ->orWhere('email', 'matches', $search)
            ->endClause();
    }

    protected function _fetchSectionItemCounts() {
        $record = $this->getRecord();

        return [
            'authentication' => $this->data->user->auth->select()
                ->where('user', '=', $record)
                ->count()
        ];
    }


// Components
    public function addIndexHeaderBarSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }

    public function addIndexHeaderBarTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('~admin/users/groups/', $this->_('Groups'))
                ->setIcon('group')
                ->setDisposition('transitive'),

            $this->html->link('~admin/users/roles/', $this->_('Roles'))
                ->setIcon('role')
                ->setDisposition('transitive'),

            $this->html->link('~admin/users/invites/', $this->_('Invites'))
                ->setIcon('mail')
                ->setDisposition('transitive')
        );
    }

    public function addDetailsSectionHeaderBarSubOperativeLinks($menu, $bar) {
        if($this->_record->hasLocalAuth()) {
            $menu->addLinks(
                // Change password
                $this->html->link(
                        $this->uri->request('~admin/users/clients/change-password?user='.$this->_record['id'], true),
                        $this->_('Change password')
                    )
                    ->setIcon('edit')
                    ->setDisposition('operative')
            );
        }

        /*
        if($this->slot->has('subOperativeLinks')) {
            $menu->addLinks($this->slot->getValue('subOperativeLinks'));
        }
        */
    }

    public function addAuthenticationSectionHeaderBarSubOperativeLinks($menu, $bar) {
        $this->addDetailsSectionHeaderBarSubOperativeLinks($menu, $bar);
    }


// Sections
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
                            $this->uri->request('~admin/users/clients/change-password?user='.$auth->getRawId('user'), true),
                            $this->_('Change password')
                        )
                        ->setIcon('edit')
                        ->setDisposition('operative');
                }
            });
    }


// Fields
    public function defineEmailField($list, $mode) {
        if($mode == 'details') {
            $list->addField('email', function($client) {
                $emailList = $this->data->user->emailVerify->fetchEmailList($client);

                return $this->html->bulletList($emailList, function($verify) use($client) {
                    $output = $this->html->link($this->uri->mailto($verify['email']), $verify['email'])
                        ->setIcon($verify['verifyDate'] ? 'tick' : 'cross')
                        ->setDisposition('transitive')
                        ->addClass($verify['email'] == $client['email'] ? null : 'state-disabled');

                    return $output;
                });
            });
        } else {
            $list->addField('email', function($client) {
                return $this->html->link($this->uri->mailto($client['email']), $client['email'])
                    ->setIcon('mail')
                    ->setDisposition('transitive');
            });
        }
    }

    public function defineStatusField($list, $mode) {
        $list->addField('status', function($client, $context) use($mode) {
            if($client['status'] == user\IState::DEACTIVATED) {
                if($mode == 'list') {
                    $context->getRowTag()->addClass('state-disabled');
                }

                $context->getCellTag()->addClass('disposition-negative');
            } else if($client['status'] == user\IState::PENDING) {
                $context->getCellTag()->addClass('state-warning');
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
                $this->html->element('p', $deactivation['reason'])
            ];

            if($deactivation['comments']) {
                $output[] = $this->html->element('div', $this->html->plainText($deactivation['comments']));
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
            $output = $this->context->i18n->countries->getName($client['country']);

            if($mode == 'list') {
                $output = $this->html->element('abbr', $client['country'], [
                    'title' => $output
                ]);
            }

            return $output;
        });
    }

    public function defineLanguageField($list) {
        $list->addField('language', function($client) {
            return $this->context->i18n->languages->getName($client['language']);
        });
    }

    public function defineTimezoneField($list) {
        $list->addField('timezone', function($client) {
            return $this->context->i18n->timezones->getName($client['timezone']);
        });
    }

    public function defineGroupsField($list, $mode) {
        $list->addField('groups', function($client) use($mode) {
            if($mode == 'list') {
                return $client['groups'];
            }

            $groupList = $client->groups->fetch()->orderBy('Name')->toArray();
        
            if(empty($groupList)) {
                return null;
            }
            
            $output = [];
            
            foreach($groupList as $group) {
                $output[] = $this->import->component('GroupLink', '~admin/users/groups/', $group);
            }
            
            return $this->html->string(implode(', ', $output));
        });
    }
}