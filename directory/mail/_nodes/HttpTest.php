<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;

class HttpTest extends arch\node\Form {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'send';

    protected function init() {

    }

    protected function setDefaultValues() {
        $this->setStore('type', 'custom');
        $this->values->type = 'custom';

        $manager = flow\Manager::getInstance();
        $this->values->transport = $manager->getDefaultMailTransportName();

        $config = flow\mail\Config::getInstance();
        $from = flow\mail\Address::factory($config->getDefaultAddress());

        $this->values->fromName = $from->getName();
        $this->values->fromAddress = $from->getAddress();

        if($this->user->isLoggedIn()) {
            $client = $this->user->client;
            $this->values->toName = $client->getFullName();
            $this->values->toAddress = $client->getEmail();
        }
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Test email'));


        // Transport
        $transportList = [];

        foreach(flow\mail\transport\Base::getAvailableTransports() as $name => $description) {
            $transportList[$name] = $name.' - '.$description;
        }


        $fs->addField($this->_('Transport'))->push(
            $this->html->radioButtonGroup('transport', $this->values->transport, $transportList)
                ->isRequired(true)
        );

        // From
        $fs->addField($this->_('From'))->push(
            $this->html->emailTextbox('fromAddress', $this->values->fromAddress)
                ->isRequired(true)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('fromName', $this->values->fromName)
                ->setPlaceholder($this->_('Name'))
        );

        // Return path
        $fs->addField($this->_('Return path'))->push(
            $this->html->emailTextbox('returnPath', $this->values->returnPath)
                ->setPlaceholder($this->_('Address'))
        );


        // To
        $fs->addField($this->_('To'))->push(
            $this->html->emailTextbox('toAddress', $this->values->toAddress)
                ->isRequired(true)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('toName', $this->values->toName)
                ->setPlaceholder($this->_('Name'))
        );


        // CC
        $fs->addField($this->_('CC'))->push(
            $this->html->emailTextbox('ccAddress', $this->values->ccAddress)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('ccName', $this->values->ccName)
                ->setPlaceholder($this->_('Name'))
        );


        // BCC
        $fs->addField($this->_('BCC'))->push(
            $this->html->emailTextbox('bccAddress', $this->values->bccAddress)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('bccName', $this->values->bccName)
                ->setPlaceholder($this->_('Name'))
        );


        $fs = $form->addFieldSet($this->_('Email body'));
        $type = $this->getStore('type');

        // Type
        $fs->addField($this->_('Email type'))->push(
            $this->html->selectList('type', $this->values->type, [
                    'prepared' => $this->_('Prepared mail preview'),
                    'custom' => $this->_('Custom text / html')
                ])
                ->isRequired(true),

            $this->html->eventButton('selectType', $this->_('Select'))
                ->setIcon('tick')
                ->setDisposition('positive')
                ->shouldValidate(false)
        );

        if($type == 'prepared') {
            // Prepared
            $fs->addField($this->_('Prepared mail'))->push(
                $this->html->selectList('prepared', $this->values->prepared, $this->_getMailList())
                    ->isRequired(true)
            );
        } else if($type == 'custom') {
            // Subject
            $fs->addField($this->_('Subject'))->push(
                $this->html->textbox('subject', $this->values->subject)
                    ->isRequired(true)
            );

            // Body text
            $fs->addField($this->_('Body text'))->push(
                $this->html->textarea('bodyText', $this->values->bodyText)
                    ->setPlaceholder($this->_('Plain text'))
            );

            // Body html
            $fs->addField($this->_('Body HTML'))->push(
                $this->html->textarea('bodyHtml', $this->values->bodyHtml)
                    ->setPlaceholder($this->_('HTML source'))
            );
        }


        // Buttons
        $form->addDefaultButtonGroup('send', $this->_('Send'));
    }

    protected function _getMailList() {
        $list = df\Launchpad::$loader->lookupFileListRecursive('apex/directory', 'php', function($path) {
            return false !== strpos($path, '_mail');
        });

        $mails = [];

        foreach($list as $name => $filePath) {
            $parts = explode('_mail/', substr($name, 0, -4), 2);
            $path = array_shift($parts);
            $name = array_shift($parts);

            if(false !== strpos($name, '/')) {
                $path .= '#/';
            }


            $name = $path.$name;
            $path = '~'.$name;

            try {
                $mail = $this->comms->prepareMail($path);
            } catch(\Exception $e) {
                $mails[$path] = null;
                continue;
            }

            $name = $path;

            if(substr($name, 0, 7) == '~front/') {
                $name = substr($name, 7);
            }

            $mails[$path] = $name;
        }

        ksort($mails);
        return $mails;
    }

    protected function onSelectTypeEvent() {
        $validator = $this->data->newValidator()
            ->addRequiredField('type', 'enum')
                ->setOptions(['prepared', 'custom'])
            ->validate($this->values);

        if($validator->isValid()) {
            $this->setStore('type', $validator['type']);

            if($validator['type'] == 'custom' && !strlen($this->values['subject'])) {
                $this->values->subject = $this->_('This is a test email from %n%', ['%n%' => $this->application->getName()]);
            }
        }
    }

    protected function onSendEvent() {
        $this->onSelectTypeEvent();

        $validator = $this->data->newValidator()

            // Transport
            ->addRequiredField('transport', 'text')
                ->setCustomValidator(function($node, $value) {
                    if(!flow\mail\transport\Base::isValidTransport($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid transport name'
                        ));
                    }
                })

            // From
            ->addRequiredField('fromAddress', 'email')
            ->addField('fromName', 'text')

            // Return path
            ->addField('returnPath', 'email')

            // To
            ->addRequiredField('toAddress', 'email')
            ->addField('toName', 'text')

            // CC
            ->addField('ccAddress', 'email')
            ->addField('ccName', 'text')

            // BCC
            ->addField('bccAddress', 'email')
            ->addField('bccName', 'text');


        switch($this->getStore('type')) {
            case 'prepared':
                return $this->_sendPrepared($validator);

            case 'custom':
                return $this->_sendCustom($validator);

            default:
                return;
        }
    }

    protected function _sendPrepared($validator) {
        $validator

            // Prepared
            ->addRequiredField('prepared', 'text')
            ->validate($this->values);

        return $this->complete(function() use($validator) {
            $transport = flow\mail\transport\Base::factory($validator['transport']);
            $mail = $this->comms->preparePreviewMail($validator['prepared']);

            $mail->setFromAddress($validator['fromAddress'], $validator['fromName']);
            $mail->addToAddress($validator['toAddress'], $validator['toName']);

            if($validator['returnPath']) {
                $mail->setReturnPath($validator['returnPath']);
            }

            if($validator['ccAddress']) {
                $mail->addCcAddress($validator['ccAddress'], $validator['ccName']);
            }

            if($validator['bccAddress']) {
                $mail->addBccAddress($validator['bccAddress'], $validator['bccAddress']);
            }

            $mail->send($transport);

            $this->comms->flashSuccess(
                'testMail.sent',
                $this->_('The email has been successfully sent')
            );
        });
    }

    protected function _sendCustom($validator) {
        $validator
            // Subject
            ->addRequiredField('subject', 'text')

            // Body
            ->addField('bodyText', 'text')
            ->addField('bodyHtml', 'text')

            ->validate($this->values);

        return $this->complete(function() use($validator) {
            $transport = flow\mail\transport\Base::factory($validator['transport']);

            $mail = new flow\mail\Message($validator['subject'], $validator['bodyHtml']);
            $mail->setFromAddress($validator['fromAddress'], $validator['fromName']);
            $mail->addToAddress($validator['toAddress'], $validator['toName']);

            if($validator['returnPath']) {
                $mail->setReturnPath($validator['returnPath']);
            }

            if($validator['ccAddress']) {
                $mail->addCcAddress($validator['ccAddress'], $validator['ccName']);
            }

            if($validator['bccAddress']) {
                $mail->addBccAddress($validator['bccAddress'], $validator['bccAddress']);
            }

            if($validator['bodyText']) {
                $mail->setBodyText($validator['bodyText']);
            }

            $mail->send($transport);

            $this->comms->flashSuccess(
                'testMail.sent',
                $this->_('The email has been successfully sent')
            );
        });
    }
}