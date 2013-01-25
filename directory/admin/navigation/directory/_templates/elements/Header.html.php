<?php
$menuId = $this['menu']->getId()->path->toString();

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/navigation/directory/edit?menu='.$menuId, true),
                $this->_('Edit menu')
            )
            ->setIcon('edit'),

        '|',

        $this->html->link(
                '~admin/navigation/directory/details?menu='.$menuId,
                $this->_('Details'),
                true
            )
            ->setIcon('details')
            ->setDisposition('informative'),

        $this->html->link(
                '~admin/navigation/directory/entries?menu='.$menuId,
                $this->_('Entries'),
                true
            )
            ->setIcon('list')
            ->setDisposition('informative'),

        '|', 

        $this->html->backLink()
    );