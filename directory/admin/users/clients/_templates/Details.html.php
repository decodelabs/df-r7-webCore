<?php
use df\user;

echo $this->import->component('DetailHeaderBar', '~admin/users/clients/', $this['client']);


echo $this->html->attributeList($this['client'])
    ->addField('fullName')
    ->addField('nickName')
    ->addField('email', function($client) {
        return $this->html->bulletList($this['emailList'], function($verify) use($client) {
            $output = $this->html->link($this->uri->mailto($verify['email']), $verify['email'])
                ->setIcon($verify['verifyDate'] ? 'tick' : 'cross')
                ->setDisposition('transitive')
                ->addClass($verify['email'] == $client['email'] ? null : 'state-disabled');

            return $output;
        });
    })
    
    ->addField('status', function($client, $context) {
        if($client['status'] == user\IState::DEACTIVATED) {
            $context->getCellTag()->addClass('disposition-negative');
        } else if($client['status'] == user\IState::PENDING) {
            $context->getCellTag()->addClass('state-warning');
        }

        return $this->context->user->client->stateIdToName($client['status']);
    })

    ->chainIf($this['client']['status'] == user\IState::DEACTIVATED, function($list) {
        $deactivation = $this->context->data->user->clientDeactivation->fetch()
            ->where('user', '=', $this['client'])
            ->toRow();

        if($deactivation) {
            $list
                ->addField('deactivateReason', function($client) use($deactivation) {
                    return $deactivation['reason'];
                })
                ->addField('deactivateComments', function($client) use($deactivation) {
                    return $this->html->plainText($deactivation['comments']);
                });
        }
    })
    
    ->addField('country', function($client) {
        return $this->context->i18n->countries->getName($client['country']);
    })
    
    ->addField('language', function($client) {
        return $this->context->i18n->languages->getName($client['language']);
    })
    
    // Join date
    ->addField('joinDate', 'Joined', function($client) {
        return $this->html->date($client['joinDate']);
    })
    
    // Login
    ->addField('loginDate', 'Last login', function($client) {
        if($client['loginDate']) {
            return $this->html->timeSince($client['loginDate']);
        }
    })
    
    // Groups
    ->addField('groups', function($client) {
        $groupList = $client->groups->fetch()->orderBy('Name')->toArray();
        
        if(empty($groupList)) {
            return null;
        }
        
        $output = [];
        
        foreach($groupList as $group) {
            $output[] = $this->import->component('GroupLink', '~admin/users/groups/', $group);
        }
        
        return $this->html->string(implode(', ', $output));
    })
;

