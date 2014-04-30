<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\directory\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class MenuList extends arch\component\template\CollectionList {

    protected $_fields = [
        'name' => true,
        'packages' => true,
        'actions' => true
    ];


// Name
    public function addNameField($list) {
        $list->addField('name', function($menu) {
            $idString = $menu->getId()->path->toString();
            $parts = explode('/', $idString);
            array_pop($parts);
            $path = implode('/', $parts);

            return $this->html->link(
                    $this->uri->request('~admin/navigation/directory/details?menu='.$idString, true),
                    [
                        $this->html->element('span.state-lowPriority', $path.'/'),
                        $menu->getDisplayName()
                    ]
                )
                ->setIcon('menu')
                ->setDisposition('informative');
        });
    }


// Packages
    public function addPackagesField($list) {
        $list->addField('packages', function($menu) {
            $output = [];

            foreach($menu->getDelegates() as $delegate) {
                $idString = $delegate->getId()->path->toString();

                if(!$subId = $delegate->getSubId()) {
                    continue;
                }

                $output[] = $this->html->link(
                        $this->uri->request('~admin/navigation/directory/details?menu='.$idString, true),
                        $this->format->name($subId)
                    )
                    ->setIcon('plugin');
            }

            return $this->html->string(implode(', ', $output));
        });
    }
    

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($menu) {
            return [
                $this->html->link(
                        $this->uri->request('~admin/navigation/directory/edit?menu='.$menu->getId()->path->toString(), true),
                        $this->_('Edit')
                    )
                    ->setIcon('edit')
            ];
        });
    }
}