<?php
echo $this->html->collectionList($this['menuList'])
    ->setErrorMessage($this->_('There are currently no system menus to display'))

    // Name
    ->addField('name', function($menu) {
        $idString = $menu->getId()->path->toString();
        $parts = explode('/', $idString);
        array_pop($parts);
        $path = implode('/', $parts);

        return $this->html->link(
                $this->uri->request('~admin/navigation/directory/details?menu='.$idString, true),
                $this->html->string(
                    '<span class="state-lowPriority">'.$this->esc($path.'/').'</span>'.
                    $this->esc($menu->getDisplayName())
                )
            )
            ->setIcon('menu')
            ->setDisposition('informative');
    })


    // Packages
    ->addField('packages', function($menu) {
        $output = array();

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
    })

    // Actions
    ->addField('actions', function($menu) {
        return [
            $this->html->link(
                    $this->uri->request('~admin/navigation/directory/edit?menu='.$menu->getId()->path->toString(), true),
                    $this->_('Edit')
                )
                ->setIcon('edit')
        ];
    })
    ;