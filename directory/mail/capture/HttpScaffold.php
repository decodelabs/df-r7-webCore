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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const TITLE = 'Development mailbox';
    const ICON = 'mail';
    const ADAPTER = 'axis://mail/Capture';
    const KEY_NAME = 'mail';
    const NAME_FIELD = 'subject';
    const CAN_ADD = false;
    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'subject', 'from', 'to', 'date',
        'isPrivate', 'environmentMode'
    ];


// Sections
    public function renderDetailsSectionBody($mail) {
        if(!$mail['readDate']) {
            $mail->readDate = 'now';
            $mail->save();
        }

        yield $this->html->flashMessage($this->_(
            'This message was received %t% ago',
            ['%t%' => $this->format->timeSince($mail['date'])]
        ));


        if($mail['isPrivate']) {
            yield $this->html->flashMessage($this->_(
                'This message is marked as private'
            ), 'warning');
        }


        /*
        yield $this->html->tag('iframe', [
            'src' => $this->uri('~mail/capture/message?mail='.$mail['id']),
            'seamless' => true,
            'style' => [
                'width' => '70em',
                'height' => '26em'
            ]
        ]);
        */

        $this->view->linkCss('theme://sass/shared/sterile.scss');
        $message = $mail->toMessage();

        yield $this->apex->template('Message.html', [
            'mail' => $mail,
            'message' => $message
        ]);
    }


// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('~mail/capture/delete-all', true),
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

            yield $this->html->link(
                    $this->uri->mailto($first->getAddress()),
                    $first->getAddress()
                )
                ->setIcon('user')
                ->setDescription($first->getName())
                ->setDisposition('external');

            if(!empty($addresses)) {
                yield $this->html(
                    '<span class="inactive">'.$this->view->esc($this->_(
                        ' and %c% more',
                        ['%c%' => count($addresses)]
                    )).'</span>'
                );
            }
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