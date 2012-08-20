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
    
class HttpDelete extends arch\form\template\Delete {

	const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'mail';

    protected $_mail;

    protected function _init() {
    	$model = $this->data->getModel('mail');

    	if(!$this->user->canAccess($model->devMail, 'delete')) {
    		$this->throwError(401, 'Cannot delete dev mail');
    	}

    	if(!$this->_mail = $model->devMail->fetchByPrimary($this->request->query['mail'])) {
    		$this->throwError(404, 'Mail not found');
    	}
    }

    protected function _getDataId() {
    	return $this->_mail['id'];
    }

    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
    	$container->push(
    		$this->html->attributeList($this->_mail)
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
			    })
		);
    }

    protected function _deleteItem() {
    	$this->_mail->delete();
    }
}