<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\dev\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
    
class HttpDelete extends arch\form\template\DeleteRecord {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'mail';
    const ENTITY_LOCATOR = 'axis://mail/DevMail';

    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['mail'],
            'delete'
        );
    }

    protected function _addAttributeListFields($attributeList) {
        $attributeList

            // Subject
            ->addField('subject')

            // From
            ->addField('from', function($mail) {
                if(!$from = $mail->getFromAddress()) {
                    return null;
                }

                return $this->html->link(
                        $this->view->uri->mailto($from->getAddress()),
                        $from->getAddress()
                    )
                    ->setIcon('user')
                    ->setDescription($from->getName());
            })

            // To
            ->addField('to', function($mail) {
                $addresses = $mail->getToAddresses();
                $first = array_shift($addresses);

                $output = [
                    $this->html->link(
                            $this->view->uri->mailto($first->getAddress()),
                            $first->getAddress()
                        )
                        ->setIcon('user')
                        ->setDescription($first->getName())
                ];

                if(!empty($addresses)) {
                    $output[] = $this->html->string(
                        '<span class="state-lowPriority">'.$this->view->esc($this->_(
                            ' and %c% more',
                            ['%c%' => count($addresses)]
                        )).'</span>'
                    );
                }

                return $output;
            })

            // Date
            ->addField('date', $this->_('Sent'), function($mail) {
                return $this->view->format->userDateTime($mail['date'], 'medium');
            })

            // Is private
            ->addField('isPrivate', $this->_('Private'), function($mail) {
                return $this->html->lockIcon($mail['isPrivate']);
            });
    }
}