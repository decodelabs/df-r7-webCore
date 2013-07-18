<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\errorLogs\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'error log';

    protected $_log;

    protected function _init() {
        $this->_log = $this->data->fetchForAction(
            'axis://error/Log',
            $this->request->query['log'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_log['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_log)
            // Date
            ->addField('date', function($log) {
                return $this->html->dateTime($log['date']);
            })

            // Code
            ->addField('code', function($log) {
                $icon = 'info';

                if($log['code'] == 404) {
                    $icon = 'warning';
                } else if($log['code'] == 500) {
                    $icon = 'error';
                }

                return $this->html->icon($icon, $log['code'])
                    ->addClass('state-'.$icon);
            })

            // User
            ->addField('user', function($log) {
                return $this->import->component('UserLink', '~admin/users/', $log['user'])
                    ->isNullable(false)
                    ->setDisposition('transitive');
            })

            // Production
            ->addField('isProduction', $this->_('Production mode'), function($log) {
                return $this->html->booleanIcon($log['isProduction']);
            })

            // Request
            ->addField('request', function($log) {
                if($log['request']) {
                    return $this->html->link($log['request'], explode('://', $log['request'])[1]);
                }
            })

            // Exception type
            ->addField('exceptionType')

            // Message
            ->addField('message', function($log) {
                return $this->html->plainText($log['message']);
            });
    }

    protected function _deleteItem() {
        $this->_log->delete();
    }
}