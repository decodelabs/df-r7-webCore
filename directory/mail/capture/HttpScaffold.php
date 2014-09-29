<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\capture;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\flow;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DIRECTORY_TITLE = 'Development mailbox';
    const DIRECTORY_ICON = 'mail';
    const RECORD_ADAPTER = 'axis://mail/Capture';
    const RECORD_KEY_NAME = 'mail';
    const RECORD_NAME_KEY = 'subject';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'subject', 'from', 'to', 'date', 
        'isPrivate', 'environmentMode', 'actions'
    ];


// Record data
    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->orWhere('subject', 'matches', $search)
            ->orWhere('body', 'matches', $search)
            ->endClause();
    }


// Sections
    public function renderDetailsSectionBody($mail) {
        if(!$mail['readDate']) {
            $mail->readDate = 'now';
            $mail->save();
        }

        $output = [
            $this->html->flashMessage($this->_(
                'This message was received %t% ago',
                ['%t%' => $this->format->timeSince($mail['date'])]
            ))
        ];


        if($mail['isPrivate']) {
            $output[] = $this->html->flashMessage($this->_(
                'This message is marked as private'
            ), 'warning');
        }


        $output[] = $this->html->tag('iframe', [
            'src' => $this->uri->request('~mail/capture/message?mail='.$mail['id']),
            'seamless' => true,
            'style' => [
                'width' => '70em',
                'height' => '26em'
            ]
        ]);

        return $output;
    }


// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~mail/capture/delete-all', true),
                    $this->_('Delete all mail')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://mail/Capture#delete')
        );
    }


// Fields
    public function defineFromField($list, $mode) {
        $list->addField('from', function($mail) {
            return $this->html->mailLink($mail['from'])
                ->setIcon('user');
        });
    }

    public function defineToField($list, $mode) {
        $list->addField('to', function($mail) {
            $addresses = flow\mail\Message::parseAddressList($mail['to']);
            $first = array_shift($addresses);

            $output = [
                $this->html->link(
                        $this->uri->mailto($first->getAddress()),
                        $first->getAddress()
                    )
                    ->setIcon('user')
                    ->setDescription($first->getName())
                    ->setDisposition('external')
            ];

            if(!empty($addresses)) {
                $output[] = $this->html->string(
                    '<span class="inactive">'.$this->view->esc($this->_(
                        ' and %c% more',
                        ['%c%' => count($addresses)]
                    )).'</span>'
                );
            }

            return $output;
        });
    }

    public function defineDateField($list, $mode) {
        $list->addField('date', function($mail, $context) use($mode) {
            if($mode == 'list' && $mail['readDate']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->userDateTime($mail['date'], 'medium');
        });
    }

    public function defineIsPrivateField($list, $mode) {
        $list->addField('isPrivate', $this->_('Private'), function($mail) {
            return $this->html->lockIcon($mail['isPrivate']);
        });
    }
}