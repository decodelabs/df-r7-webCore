<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\logs;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    const DIRECTORY_TITLE = 'Spool logs';
    const DIRECTORY_ICON = 'log';
    const RECORD_ADAPTER = 'axis://task/Log';
    const RECORD_NAME_KEY = 'request';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'request', 'startDate', 'runTime',
        'status', 'environmentMode', 'actions'
    ];

    protected $_recordDetailsFields = [
        'id', 'request', 'environmentMode', 'startDate', 'runTime',
        'status'
    ];


// Sections
    public function renderDetailsSectionBody($log) {
        $output = [parent::renderDetailsSectionBody($log)];

        if($log['errorOutput']) {
            $output[] = [
                $this->html->element('h3', $this->_('Error output')),
                $this->html->container($this->html->plainText($log['errorOutput']))
                    ->addClass('error mono')
            ];
        }

        if($log['output']) {
            $output[] = [
                $this->html->element('h3', $this->_('Standard output')),
                $this->html->container($this->html->plainText($log['output']))
                    ->addClass('mono')
            ];
        }

        return $output;
    }


// Components
    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/processes/logs/delete-all', true),
                    $this->_('Delete all logs')
                )
                ->setIcon('delete')
        );
    }


// Fields
    public function defineStartDateField($list, $mode) {
        $list->addField('startDate', $this->_('Started'), function($log) {
            return $this->html->timeFromNow($log['startDate']);
        });
    }

    public function defineRunTimeField($list, $mode) {
        $list->addField('runTime', function($log) {
            return $this->format->duration($log['runTime']);
        });
    }

    public function defineStatusField($list, $mode) {
        $list->addField('status', function($log) {
            if($log['errorOutput']) {
                return $this->html->icon('error', $this->_('Error'))
                    ->addClass('error');
            } else if(!$log['output']) {
                return $this->html->icon('warning', $this->_('No output'))
                    ->addClass('warning');
            } else {
                return $this->html->icon('tick', $this->_('Success'))
                    ->addClass('success');
            }
        });
    }
}