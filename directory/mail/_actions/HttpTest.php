<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;

class HttpTest extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'send';

    protected function init() {

    }

    protected function setDefaultValues() {
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

        $this->values->subject = $this->_('This is a test email from %n%', ['%n%' => $this->application->getName()]);
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


        // Buttons
        $fs->addDefaultButtonGroup('send', $this->_('Send'));
    }

    protected function onSendEvent() {
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
            ->addField('bccName', 'text')

            // Subject
            ->addRequiredField('subject', 'text')

            // Body
            ->addField('bodyText', 'text')
            ->addField('bodyHtml', 'text')

            ->validate($this->values);


        return $this->complete(function() use($validator) {
            $transport = flow\mail\transport\Base::factory($validator['transport']);

            $mail = new flow\mail\Message();
            $mail->setFromAddress($validator['fromAddress'], $validator['fromName']);
            $mail->addToAddress($validator['toAddress'], $validator['toName']);

            if($validator['returnPath']) {
                $mail->setReturnPath($validator['returnPath']);
            }

            if($validator['ccAddress']) {
                $mail->addCCAddress($validator['ccAddress'], $validator['ccName']);
            }

            if($validator['bccAddress']) {
                $mail->addBCCAddress($validator['bccAddress'], $validator['bccAddress']);
            }

            $mail->setSubject($validator['subject']);

            if($validator['bodyText']) {
                $mail->setBodyText($validator['bodyText']);
            }

            if($validator['bodyHtml']) {
                $mail->setBodyHtml($validator['bodyHtml']);
            }

            $transport->send($mail);

            $this->comms->flashSuccess(
                'testMail.sent',
                $this->_('The email has been successfully sent')
            );
        });
    }
}