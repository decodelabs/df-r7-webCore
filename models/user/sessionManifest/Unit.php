<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\sessionManifest;

use df;
use df\core;
use df\apex;
use df\axis;
use df\user;

class Unit extends axis\unit\table\Base implements user\session\IBackend {

    protected $_lifeTime = 86400; // 24 hours
    protected $_dataTransactions = array();

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addPrimaryField('internalId', 'String', 40);
        $schema->addUniqueField('externalId', 'String', 40);
        $schema->addUniqueField('transitionId', 'String', 40)->isNullable(true);
        $schema->addField('startTime', 'Integer');
        $schema->addField('transitionTime', 'Integer', 8)->isNullable(true);
        $schema->addIndexedField('accessTime', 'Integer', 8);
        //$schema->addField('userId', 'String', 64)->isNullable(true);
    }

// Life time
    public function setLifeTime($lifeTime) {
        if($lifeTime instanceof core\time\IDuration) {
            $lifeTime = $lifeTime->getSeconds();
        }
        
        $this->_lifeTime = (int)$lifeTime;
        return $this;
    }
    
    public function getLifeTime() {
        return $this->_lifeTime;
    }
    

// Descriptor
    public function insertDescriptor(user\session\IDescriptor $descriptor) {
        $this->insert($descriptor)->execute();
        return $descriptor;
    }

    public function fetchDescriptor($id, $transitionTime) {
        $output = $this->select()
            ->where('externalId', '=', $id)
            ->beginOrWhereClause()
                ->where('transitionId', '=', $id)
                ->where('transitionTime', '>=', $transitionTime)
                ->endClause()
            ->toRow();

        if(!empty($output)) {
            $output = user\session\Descriptor::fromArray($output);
        }
        
        return $output;
    }

    public function touchSession(user\session\IDescriptor $descriptor) {
        $values = $descriptor->touchInfo(user\session\Controller::TRANSITION_LIFETIME);
        
        $this->update($values)
            ->where('internalId', '=', $descriptor->internalId)
            ->execute();
        
        return $descriptor;
    }

    public function applyTransition(user\session\IDescriptor $descriptor) {
        $this->update([
                'accessTime' => $descriptor->getAccessTime(),
                'externalId' => $descriptor->getExternalId(),
                'transitionId' => $descriptor->getTransitionId(),
                'transitionTime' => $descriptor->getTransitionTime()
            ])
            ->where('internalId', '=', $descriptor->getInternalId())
            ->execute();
            
        return $descriptor;
    }

    public function killSession(user\session\IDescriptor $descriptor) {
        $id = $descriptor->getInternalId();
        
        if(isset($this->_dataTransactions[$id])) {
            $this->_dataTransactions[$id]->commit();
        }
        
        $this->delete()
            ->where('internalId', '=', $id)
            ->execute();

        $this->_model->sessionData->delete()
            ->where('internalId', '=', $id)
            ->execute();

        unset($this->_dataTransactions[$id]);
        
        return $this;
    }

    public function idExists($id) {
        return (bool)$this->select('COUNT(*) as count')
            ->where('internalId', '=', $id)
            ->orWhere('externalId', '=', $id)
            ->orWhere('transitionId', '=', $id)
            ->toValue('count');
    }
    

// Namespace
    public function getNamespaceKeys(user\session\IDescriptor $descriptor, $namespace) {
        return $this->_model->sessionData->select('key')
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->orderBy('updateTime')
            ->toList('key');
    }

    public function pruneNamespace(user\session\IDescriptor $descriptor, $namespace, $age) {
        $this->_model->sessionData->delete()
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->where('updateTime', '<', time() - $age)
            ->where('updateTime', '!=', null)
            ->execute();
    }

    public function clearNamespace(user\session\IDescriptor $descriptor, $namespace) {
        $this->_model->sessionData->delete()
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->execute();
    }
    
    public function clearNamespaceForAll($namespace) {
        $this->_model->sessionData->delete()
            ->where('namespace', '=', $namespace)
            ->execute();
    }



// Nodes
    public function fetchNode(user\session\IDescriptor $descriptor, $namespace, $key) {
        $res = $this->_model->sessionData->select()
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key)
            ->toRow();
            
        return user\session\Handler::createNode($namespace, $key, $res);
    }

    public function fetchLastUpdatedNode(user\session\IDescriptor $descriptor, $namespace) {
        $res = $this->_model->sessionData->select()
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->orderBy('updateTime DESC')
            ->toRow();
            
        if($res) {
            return user\session\Handler::createNode($namespace, $res['key'], $res);
        } else {
            return null;
        }
    }

    public function lockNode(user\session\IDescriptor $descriptor, \stdClass $node) {
        $this->_beginDataTransaction($descriptor);
        $node->isLocked = true;
        
        return $node;
    }

    public function unlockNode(user\session\IDescriptor $descriptor, \stdClass $node) {
        if($transaction = $this->_getDataTransaction($descriptor)) {
            $transaction->commit();
        }
        
        return $node;
    }

    public function updateNode(user\session\IDescriptor $descriptor, \stdClass $node) {
        if($transaction = $this->_getDataTransaction($descriptor)) {
            if(empty($node->creationTime)) {
                $node->creationTime = time();
                
                $transaction->insert([
                        'internalId' => $descriptor->getInternalId(),
                        'namespace' => $node->namespace,
                        'key' => $node->key,
                        'value' => serialize($node->value),
                        'creationTime' => $node->creationTime,
                        'updateTime' => $node->updateTime
                    ])
                    ->execute();
            } else {
                $transaction->update([
                        'value' => serialize($node->value),
                        'updateTime' => $node->updateTime
                    ])
                    ->where('internalId', '=', $descriptor->getInternalId())
                    ->where('namespace', '=', $node->namespace)
                    ->where('key', '=', $node->key)
                    ->execute();
            }
        }
        
        return $node;
    }

    public function removeNode(user\session\IDescriptor $descriptor, $namespace, $key) {
        $this->_model->sessionData->delete()
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key)
            ->execute();
    }

    public function hasNode(user\session\IDescriptor $descriptor, $namespace, $key) {
        return (bool)$this->_model->sessionData->select('count(*) as count')
            ->where('internalId', '=', $descriptor->getInternalId())
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key)
            ->toValue('count');
    }

    public function collectGarbage() {
        $time = time() - $this->_lifeTime;

        $this->_model->sessionData->delete()
            ->whereCorrelation('internalId', 'in', 'internalId')
                ->from($this, 'manifest')
                ->where('manifest.accessTime', '<', $time)
                ->endCorrelation()
            ->beginOrWhereClause()
                ->where('sessionData.updateTime', '!=', null)
                ->where('sessionData.updateTime', '<', $time)
                ->endClause()
            ->beginOrWhereClause()
                ->where('sessionData.updateTime', '=', null)
                ->where('sessionData.creationTime', '<', $time)
                ->endClause()
            ->execute();
        
        $this->delete()
            ->where('accessTime', '<', $time)
            ->execute();
            
        return $this;
    }

    protected function _getDataTransaction(user\session\IDescriptor $descriptor) {
        $id = $descriptor->getInternalId();
        
        if(isset($this->_dataTransactions[$id])) {
            return $this->_dataTransactions[$id];
        }
        
        return null;
    }
    
    protected function _beginDataTransaction(user\session\IDescriptor $descriptor) {
        $id = $descriptor->getInternalId();
        
        if(isset($this->_dataTransactions[$id])) {
            $output = $this->_dataTransactions[$id];
            $output->beginAgain();
        } else {
            $output = $this->_dataTransactions[$id] = $this->_model->sessionData->begin();
        }
        
        return $output;
    }
}