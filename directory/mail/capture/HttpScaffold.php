<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\capture;

use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;

use DecodeLabs\R7\Legacy;
use DecodeLabs\Tagged as Html;
use df\arch;
use df\flow;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const TITLE = 'Development mailbox';
    public const ICON = 'mail';
    public const ADAPTER = 'axis://mail/Capture';
    public const KEY_NAME = 'mail';
    public const NAME_FIELD = 'subject';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'subject', 'from', 'to', 'date', 'environmentMode'
    ];


    // Sections
    public function renderDetailsSectionBody($mail)
    {
        if (!$mail['readDate']) {
            $mail->readDate = 'now';
            $mail->save();
        }

        yield $this->html->flashMessage(
            'This message was received ' . Dictum::$time->since($mail['date'])
        );


        $this->view->linkCss('theme://css/sterile.scss');
        $message = $mail->toMessage();

        yield $this->apex->template('Message.html', [
            'mail' => $mail,
            'message' => $message
        ]);
    }

    public function downloadNode()
    {
        $mail = $this->getRecord();
        $message = $mail->toMessage();
        $partIds = explode('-', $this->request['part']);
        array_shift($partIds);

        if (!$part = $this->_getMessagePart($message, $partIds)) {
            throw Exceptional::NotFound([
                'message' => 'Part not found',
                'http' => 404
            ]);
        }

        $content = $part->getContentString();
        $contentType = $part->getContentType();

        if (!$filename = $part->getFilename()) {
            $filename = $mail['id'] . '-' . $this->request['part'];
        }

        return Legacy::$http->stringResponse($content, $contentType)
            ->setFilename($filename);
    }

    private function _getMessagePart($multipart, $partIds)
    {
        $partId = (int)array_shift($partIds);

        if (!$part = $multipart->getPart($partId)) {
            return null;
        }

        if (!empty($partIds)) {
            if (!$part instanceof flow\mime\IMultiPart) {
                return null;
            }

            return $this->_getMessagePart($part, $partIds);
        } else {
            if (!$part instanceof flow\mime\IContentPart) {
                return null;
            }

            return $part;
        }
    }


    // Components
    public function generateIndexSubOperativeLinks(): iterable
    {
        yield 'deleteAll' => $this->html->link(
            $this->uri('~mail/capture/delete-all', true),
            $this->_('Delete all mail')
        )
            ->setIcon('delete')
            ->addAccessLock('axis://mail/Capture#delete');
    }


    // Fields
    public function defineFromField($list, $mode)
    {
        $list->addField('from', function ($mail) {
            return $this->html->mailLink($mail['from'])
                ->setIcon('user');
        });
    }

    public function defineToField($list, $mode)
    {
        $list->addField('to', function ($mail) {
            $addresses = flow\mail\AddressList::factory($mail['to']);
            $first = $addresses->extract();

            yield $this->html->link(
                $this->uri->mailto($first->getAddress()),
                $first->getAddress()
            )
                ->setIcon('user')
                ->setDescription($first->getName())
                ->setDisposition('external');

            if (!$addresses->isEmpty()) {
                yield Html::raw(
                    '<span class="inactive">' . Html::esc($this->_(
                        ' and %c% more',
                        ['%c%' => count($addresses)]
                    )) . '</span>'
                );
            }
        });
    }

    public function defineDateField($list, $mode)
    {
        $list->addField('date', function ($mail, $context) use ($mode) {
            if ($mode == 'list' && $mail['readDate']) {
                $context->getRowTag()->addClass('inactive');
            }

            return Html::$time->dateTime($mail['date']);
        });
    }
}
