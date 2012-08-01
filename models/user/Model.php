<?php

namespace df\apex\models\user;

use df\core;
use df\axis;
use df\user;

class Model extends axis\Model implements user\IUserModel {
    
    public function getAuthenticationDomainInfo(user\authentication\IRequest $request) {
        return $this->getUnit('auth')->fetch()
            ->where('identity', '=', $request->getIdentity())
            ->where('adapter', '=', $request->getAdapterName())
            ->toRow();
    }
    
    public function generateKeyring(user\IClient $client) {
        $state = $client->getAuthenticationState();
        $id = $client->getId();

        $query = $this->role->select('id');
        
        if($state >= user\IState::BOUND && $id !== null) {
            $groupBridge = $this->group->getUnitSchema()
                ->getField('roles')
                ->getBridgeUnit($this->_application);
                
            $clientBridge = $this->client->getUnitSchema()
                ->getField('groups')
                ->getBridgeUnit($this->_application);


            $query
                ->wherePrerequisite('minRequiredState', '<=', $state)

                ->whereCorrelation('id', 'in', 'role')
                    ->from($groupBridge, 'groupBridge')
                    ->joinConstraint()
                        ->from($clientBridge, 'clientBridge')
                        ->on('clientBridge.group', '=', 'group')
                        ->endJoin()
                    ->where('clientBridge.isLeader', '=', false)
                    ->where('clientBridge.client', '=', $id)
                    ->endCorrelation()
                    
                ->beginOrWhereClause()
                    ->where('bindState', '>=', user\IState::BOUND)
                    ->where('bindState', '<=', $state)
                    ->endClause()
                    ;
        } else {
            $query->where('bindState', '=', $state)
                ->where('minRequiredState', '<=', $state);
        }
        
        $query
            ->attach('domain', 'pattern', 'allow')
                ->from($this->key, 'key')
                ->on('key.role', '=', 'role.id')
                ->asMany('keys')
            ->orderBy('priority ASC');
        
        $output = array();

        foreach($query as $role) {
            foreach($role['keys'] as $key) {
                if(!isset($output[$key['domain']])) {
                    $output[$key['domain']] = array();
                }
                
                $output[$key['domain']][$key['pattern']] = (bool)$key['allow'];
            }
        }
        
        return $output;
    }
}