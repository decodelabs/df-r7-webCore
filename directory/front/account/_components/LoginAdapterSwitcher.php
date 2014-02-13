<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\user;
    
class LoginAdapterSwitcher extends arch\component\template\FormUi {

    protected function _execute(array $enabled, $current) {
        $menu = $this->content->addMenuBar();

        foreach($enabled as $adapterName => $options) {
            $class = 'df\\user\\authentication\\adapter\\'.$adapterName;

            if(!class_exists($class)) {
                continue;
            }

            $name = $class::getDisplayName();

            $menu->addLink(
                $this->html->link(
                        $this->view->uri->query(['adapter' => $adapterName]),
                        $name
                    )
                    ->isActive($adapterName == $current)
            );
        }
    }
}