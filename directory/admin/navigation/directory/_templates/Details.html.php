<?php

echo $this->import->component('~admin/navigation/directory/DetailHeaderBar', $this['menu']);


echo $this->html->attributeList($this['menu'])

    // Id
    ->addField('id', $this->_('Full ID'), function($menu) {
        return $menu->getId();
    })

    // Name
    ->addField('name', function($menu) {
        return $menu->getDisplayName();
    })

    // Area
    ->addField('area', function($menu) {
        return $menu->getId()->path->getFirst();
    })

    // Parent
    ->addField('parent', function($menu) {
        if($subId = $menu->getSubId()) {
            $id = substr($menu->getId()->path->toString(), 0, -strlen($subId) - 1);

            return $this->html->link(
                    $this->uri('~admin/navigation/directory/details?menu='.$id, true),
                    'Directory://'.$id
                )
                ->setIcon('menu')
                ->setDisposition('informative');
        }
    })

    // Package
    ->addField('package', $this->_('Delegate package'), function($menu) {
        return $menu->getSubId();
    })

    // Delegates
    ->addField('delegates', function($menu) {
        $delegates = $menu->getDelegates();

        if(!empty($delegates)) {
            return $this->html->bulletList($delegates)
                ->setRenderer(function($delegate) {
                    if($delegate->getSourceId() == 'Directory') {
                        return $this->html->link(
                                $this->uri('~admin/navigation/directory/details?menu='.$delegate->getId()->path->toString()),
                                $delegate->getId()
                            )
                            ->setIcon('menu')
                            ->setDisposition('informative');
                    } else {
                        return $this->html->icon('menu', $delegate->getId())
                            ->setTitle($delegate->getDisplayName());
                    }
                });
        }
    })
    ;


echo $this->html('h3', $this->_('Entries'));

echo $this->import->template('~admin/navigation/#/elements/MenuEntryList.html');
