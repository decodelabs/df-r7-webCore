<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\criticalErrors\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'error log';

    protected $_error;

    protected function _init() {
        $this->_error = $this->data->fetchForAction(
            'axis://log/CriticalError',
            $this->request->query['error'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_error['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_error)
            // Date
            ->addField('date', function($error) {
                return $this->html->dateTime($error['date']);
            })

            // User
            ->addField('user', function($error) {
                return $this->import->component('UserLink', '~admin/users/clients/', $error['user'])
                    ->isNullable(true)
                    ->setDisposition('transitive');
            })

            // Production
            ->addField('isProduction', $this->_('Production mode'), function($error) {
                return $this->html->booleanIcon($error['isProduction']);
            })

            // Request
            ->addField('request', function($error) {
                if($error['request']) {
                    return $this->html->link($error['request'], explode('://', $error['request'])[1]);
                }
            })

            // Exception type
            ->addField('exceptionType')

            // Message
            ->addField('message', function($error) {
                return $this->html->plainText($error['message']);
            })

            // File
            ->addField('file', function($error) {
                if($error['file']) {
                    return $error['file'].' : '.$error['line'];
                }
            })

            // Frequency
            ->addField('frequency', function($error) {
                return [
                    $this->_('This error has been seen %n% times', ['%n%' => $error->fetchFrequency()]), $this->html->string('<br /><br />'),
                    $this->html->checkbox('deleteAll', $this->values->deleteAll, $this->_(
                        'Delete all instances of this error'
                    ))
                ];
            })
            ;
    }

    protected function _deleteItem() {
        $this->_error->delete();

        if($this->values['deleteAll']) {
            $this->data->log->criticalError->delete()
                ->where('exceptionType', '=', $this->_error['exceptionType'])
                ->where('message', '=', $this->_error['message'])
                ->execute();
        }
    }
}