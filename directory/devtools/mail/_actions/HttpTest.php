<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;
    
class HttpTest extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'send';

    protected function _init() {

    }

    protected function _setDefaultValues() {
        $this->values->transport = flow\mail\transport\Base::getDefaultTransportName();

        $config = flow\mail\Config::getInstance($this->getApplication());
        $from = flow\mail\Address::factory($config->getDefaultAddress());

        $this->values->fromName = $from->getName();
        $this->values->fromAddress = $from->getAddress();

        if($this->user->isLoggedIn()) {
            $client = $this->user->client;
            $this->values->toName = $client->getFullName();
            $this->values->toAddress = $client->getEmail();
        }

        $this->values->subject = $this->_('This is a test email from %n%', ['%n%' => $this->getApplication()->getName()]);
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Test email'));


        // Transport
        $transportList = array();

        foreach(flow\mail\transport\Base::getAvailableTransports() as $name => $description) {
            $transportList[$name] = $name.' - '.$description;
        }


        $fs->addFieldArea($this->_('Transport'))->push(
            $this->html->radioButtonGroup('transport', $this->values->transport, $transportList)
                ->isRequired(true)
        );

        // From
        $fs->addFieldArea($this->_('From'))->push(
            $this->html->emailTextbox('fromAddress', $this->values->fromAddress)
                ->isRequired(true)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('fromName', $this->values->fromName)
                ->setPlaceholder($this->_('Name'))
        );


        // To
        $fs->addFieldArea($this->_('To'))->push(
            $this->html->emailTextbox('toAddress', $this->values->toAddress)
                ->isRequired(true)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('toName', $this->values->toName)
                ->setPlaceholder($this->_('Name'))
        );


        // CC
        $fs->addFieldArea($this->_('CC'))->push(
            $this->html->emailTextbox('ccAddress', $this->values->ccAddress)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('ccName', $this->values->ccName)
                ->setPlaceholder($this->_('Name'))
        );


        // BCC
        $fs->addFieldArea($this->_('BCC'))->push(
            $this->html->emailTextbox('bccAddress', $this->values->bccAddress)
                ->setPlaceholder($this->_('Address')),

            $this->html->textbox('bccName', $this->values->bccName)
                ->setPlaceholder($this->_('Name'))
        );


        // Subject
        $fs->addFieldArea($this->_('Subject'))->push(
            $this->html->textbox('subject', $this->values->subject)
                ->isRequired(true)
        );



        // Body text
        $fs->addFieldArea($this->_('Body text'))->push(
            $this->html->textarea('bodyText', $this->values->bodyText)
                ->setPlaceholder($this->_('Plain text'))
        );

        // Body html
        $fs->addFieldArea($this->_('Body HTML'))->push(
            $this->html->textarea('bodyHtml', $this->values->bodyHtml)
                ->setPlaceholder($this->_('HTML source'))
        );


        // Buttons
        $fs->push($this->html->defaultButtonGroup('send', $this->_('Send')));
    }

    protected function _onSendEvent() {
        $this->data->newValidator()

            // Transport
            ->addField('transport', 'text')
                ->isRequired(true)
                ->setCustomValidator(function($node, $value) {
                    if(!flow\mail\transport\Base::isValidTransport($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid transport name'
                        ));
                    }
                })
                ->end()

            // From
            ->addField('fromAddress', 'email')
                ->isRequired(true)
                ->end()
            ->addField('fromName', 'text')
                ->end()

            // To
            ->addField('toAddress', 'email')
                ->isRequired(true)
                ->end()
            ->addField('toName', 'text')
                ->end()

            // CC
            ->addField('ccAddress', 'email')
                ->end()
            ->addField('ccName', 'text')
                ->end()

            // BCC
            ->addField('bccAddress', 'email')
                ->end()
            ->addField('bccName', 'text')
                ->end()

            // Subject
            ->addField('subject', 'text')
                ->isRequired(true)
                ->end()

            // Body
            ->addField('bodyText', 'text')
                ->end()
            ->addField('bodyHtml', 'text')
                ->end()


            ->validate($this->values);



        if($this->isValid()) {
            $transport = flow\mail\transport\Base::factory($this->values['transport']);

            $mail = new flow\mail\Message();
            $mail->setFromAddress($this->values['fromAddress'], $this->values['fromName']);
            $mail->addToAddress($this->values['toAddress'], $this->values['toName']);

            if($this->values['ccAddress']) {
                $mail->addCCAddress($this->values['ccAddress'], $this->values['ccName']);
            }

            if($this->values['bccAddress']) {
                $mail->addBCCAddress($this->values['bccAddress'], $this->values['bccAddress']);
            }

            $mail->setSubject($this->values['subject']);

            if($this->values['bodyText']) {
                $mail->setBodyText($this->values['bodyText']);
            }

            if($this->values['bodyHtml']) {
                $mail->setBodyHtml($this->values['bodyHtml']);
            }

            $transport->send($mail);

            $this->comms->flash(
                'testMail.sent', 
                $this->_('The email has been successfully sent'), 
                'success'
            );
            
            return $this->complete();
        }
    }
}