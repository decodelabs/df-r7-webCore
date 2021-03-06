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

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Users';
    const ICON = 'user';
    const ADAPTER = 'axis://user/Client';
    const KEY_NAME = 'user';
    const NAME_FIELD = 'fullName';

    const SECTIONS = [
        'details',
        //'invites' => 'mail',
        'authentication' => 'lock',
        'sessions' => 'time',
        'accessPasses' => 'key'
    ];

    const LIST_FIELDS = [
        'fullName', 'email', 'status', 'groups',
        'country', 'joinDate', 'loginDate'
    ];

    const DETAILS_FIELDS = [
        'fullName', 'nickName', 'email', 'status',
        'deactivation', 'country', 'language',
        'timezone', 'joinDate', 'loginDate', 'groups'
    ];

    const CONFIRM_DELETE = true;


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->countRelation('groups');
    }

    protected function countSectionItems($record): array
    {
        return $this->getRecordAdapter()->select('id')
            ->correlate('COUNT(*) as invites')
                ->from('axis://user/Invite')
                ->on('owner', '=', 'id')
                ->endCorrelation()
            ->correlate('COUNT(*) as authentication')
                ->from('axis://user/Auth')
                ->on('user', '=', 'id')
                ->endCorrelation()
            ->correlate('COUNT(*) as accessPasses')
                ->from('axis://user/AccessPass')
                ->on('user', '=', 'id')
                ->endCorrelation()
            ->where('id', '=', $record['id'])
            ->toRow();
    }


    // Nodes
    public function confirmedHtmlNode()
    {
        return $this->buildRecordListNode(function ($query) {
            $query->where('status', '=', user\IState::CONFIRMED);
        });
    }

    public function deactivatedHtmlNode()
    {
        return $this->buildRecordListNode(function ($query) {
            $query->where('status', '=', user\IState::DEACTIVATED);
        });
    }

    public function spamHtmlNode()
    {
        return $this->buildRecordListNode(function ($query) {
            $query->where('status', '=', user\IState::SPAM);
        });
    }


    // Filters
    public function generateRecordFilters(): iterable
    {
        yield $this->newRecordFilter('group', 'All users', function () {
            yield from $this->data->user->group->select('id', 'name')
                ->orderBy('name ASC')
                ->toList('id', 'name');
        })->setApplicator(function ($query, $groupId) {
            $query->whereCorrelation('id', 'in', 'client')
                ->from($this->data->user->client->getBridgeUnit('groups'), 'bridge')
                ->where('group', '=', $groupId)
                ->endCorrelation();
        });
    }


    // Sections
    public function renderDetailsSectionBody($client)
    {
        return $this->html->panelSet()
            ->addPanel(parent::renderDetailsSectionBody($client))
            ->addPanel([
                $this->html->image(
                    $this->avatar->getAvatarUrl($client['id'], 400),
                    'Avatar'
                )
            ]);
    }

    public function renderInvitesSectionBody($client)
    {
        return $this->apex->scaffold('../invites/')
            ->renderRecordList(function ($query) use ($client) {
                $query->where('owner', '=', $client['id']);
            }, [
                'owner' => false
            ]);
    }

    public function renderAuthenticationSectionBody($client)
    {
        $authenticationList = $client->authDomains->fetch()
            ->orderBy('adapter ASC');

        return $this->html->collectionList($authenticationList)
            ->setErrorMessage($this->_('There are no authentication entries to display'))

            // Adapter
            ->addField('adapter')

            // Identity
            ->addField('identity')

            // Bind date
            ->addField('bindDate', function ($auth) {
                return Html::$time->date($auth['bindDate']);
            })

            // Login date
            ->addField('loginDate', $this->_('Last login'), function ($auth) {
                if ($auth['loginDate']) {
                    return Html::$time->since($auth['loginDate']);
                }
            })

            // Actions
            ->addField('actions', function ($auth) {
                if ($auth['adapter'] == 'Local') {
                    return $this->html->link(
                            $this->uri('./change-password?user='.$auth['#user'], true),
                            $this->_('Change password')
                        )
                        ->setIcon('edit')
                        ->setDisposition('operative');
                }
            });
    }

    public function renderSessionsSectionBody($client)
    {
        $sessions = $this->data->session->descriptor->select()
            ->correlate('COUNT(*)', 'nodes')
                ->from('axis://session/Node', 'node')
                ->on('node.descriptor', '=', 'id')
                ->endCorrelation()
            ->where('user', '=', $client['id'])
            ->orderBy('accessTime DESC');

        yield $this->html->collectionList($sessions)
            ->addField('id', function ($session) {
                return bin2hex($session['id']);
            })
            ->addField('startTime', function ($session) {
                return Html::$time->since($session['startTime']);
            })
            ->addField('transitionTime', function ($session) {
                return Html::$time->since($session['transitionTime']);
            })
            ->addField('accessTime', function ($session) {
                return Html::$time->since($session['accessTime']);
            });
    }

    public function renderAccessPassesSectionBody($client)
    {
        return $this->apex->scaffold('../access-passes/')
            ->renderRecordList(function ($query) use ($client) {
                $query->where('user', '=', $client['id']);
            }, [
                'user' => false
            ]);
    }



    // Components
    public function generateIndexSectionLinks(): iterable
    {
        yield 'all' => $this->html->link('./', $this->_('All'), true)
            ->setIcon('star')
            ->setDisposition('informative');

        yield 'confirmed' => $this->html->link('./confirmed', $this->_('Confirmed'), true)
            ->setIcon('tick')
            ->setDisposition('informative');

        yield 'deactivated' => $this->html->link('./deactivated', $this->_('Deactivated'), true)
            ->setIcon('remove')
            ->setDisposition('informative');

        yield 'spam' => $this->html->link('./spam', $this->_('Spam'), true)
            ->setIcon('warning')
            ->setDisposition('informative');
    }

    public function generateIndexSubOperativeLinks(): iterable
    {
        yield $this->html->link(
                $this->uri('../settings', true),
                $this->_('Settings')
            )
            ->setIcon('settings')
            ->setDisposition('operative');
    }

    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'groups' => $this->html->link('../groups/', $this->_('Groups'))
            ->setIcon('group')
            ->setDisposition('transitive');

        yield 'roles' => $this->html->link('../roles/', $this->_('Roles'))
            ->setIcon('role')
            ->setDisposition('transitive');

        yield 'invites' => $this->html->link('../invites/', $this->_('Invites'))
            ->setIcon('mail')
            ->setDisposition('transitive');
    }

    public function generateDetailsSectionSubOperativeLinks(): iterable
    {
        // Change password
        yield 'changePassword' => $this->html->link(
                $this->uri('./change-password?user='.$this->getRecordId(), true),
                $this->_('Change password')
            )
            ->setIcon('edit')
            ->setDisposition('operative');
    }

    public function generateAuthenticationSectionSubOperativeLinks(): iterable
    {
        yield from $this->generateDetailsSectionSubOperativeLinks();
    }

    public function generateAccessPassesSectionSubOperativeLinks(): iterable
    {
        yield 'add' => $this->html->link(
                $this->uri('../access-passes/add?user='.$this->getRecordId(), true),
                $this->_('Add access pass')
            )
            ->setIcon('add');
    }


    // Fields
    public function defineEmailField($list, $mode)
    {
        if ($mode == 'details') {
            $list->addField('email', function ($client) {
                $emailList = $this->data->user->emailVerify->fetchEmailList($client);

                return Html::uList($emailList, function ($verify) use ($client) {
                    $output = $this->html->mailLink($verify['email'])
                        ->setIcon($verify['verifyDate'] ? 'tick' : 'cross')
                        ->addClass($verify['email'] == $client['email'] ? null : 'disabled');

                    return $output;
                });
            });
        } else {
            $list->addField('email', function ($client) {
                return $this->html->mailLink($client['email']);
            });
        }
    }

    public function defineStatusField($list, $mode)
    {
        $list->addField('status', function ($client, $context) use ($mode) {
            if (in_array($client['status'], [user\IState::SPAM, user\IState::DEACTIVATED])) {
                if ($mode == 'list') {
                    $context->getRowTag()->addClass('inactive');
                }

                $context->getCellTag()->addClass('negative');

                if ($client['status'] === user\IState::SPAM) {
                    yield $this->html->icon('warning');
                }
            } elseif ($client['status'] == user\IState::PENDING) {
                $context->getCellTag()->addClass('warning');
            }

            yield $this->user->client->stateIdToName($client['status']);
        });
    }

    public function defineDeactivationField($list, $mode)
    {
        if ($mode != 'details') {
            return;
        }

        $list->addField('deactivation', function ($client, $context) {
            if ($client['status'] != user\IState::DEACTIVATED) {
                return $context->skipRow();
            }

            $deactivation = $this->context->data->user->clientDeactivation->fetch()
                ->where('user', '=', $client)
                ->toRow();

            if (!$deactivation) {
                return $context->skipRow();
            }

            $output = [
                Html::{'p'}($deactivation['reason'])
            ];

            if ($deactivation['comments']) {
                $output[] = Html::{'div'}($this->html->plainText($deactivation['comments']));
            }

            return $output;
        });
    }

    public function defineLoginDateField($list, $mode)
    {
        $list->addField('loginDate', $mode == 'list' ? $this->_('Login') : $this->_('Last login'), function ($client) {
            if ($client['loginDate']) {
                return Html::$time->since($client['loginDate']);
            }
        });
    }

    public function defineCountryField($list, $mode)
    {
        $list->addField('country', function ($client) use ($mode) {
            $output = $this->i18n->countries->getName($client['country']);

            if ($mode == 'list') {
                $output = Html::{'abbr'}($client['country'], [
                    'title' => $output
                ]);
            }

            return $output;
        });
    }

    public function defineLanguageField($list)
    {
        $list->addField('language', function ($client) {
            return $this->i18n->languages->getName($client['language']);
        });
    }

    public function defineTimezoneField($list)
    {
        $list->addField('timezone', function ($client) {
            return $this->i18n->timezones->getName($client['timezone']);
        });
    }

    public function defineGroupsField($list, $mode)
    {
        $list->addField('groups', function ($client) use ($mode) {
            if ($mode == 'list') {
                return $client['groups'];
            }

            $groupList = $client->groups->fetch()->orderBy('Name');

            return Html::uList($groupList, function ($group) {
                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }
}
