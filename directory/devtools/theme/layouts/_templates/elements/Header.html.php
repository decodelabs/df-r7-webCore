<?php

$layoutId = $this['layout']->getId();

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri('~devtools/theme/layouts/edit?layout='.$layoutId, true),
                $this->_('Edit layout')
            )
            ->setIcon('edit'),

        $this->html->link(
                $this->uri(
                    '~devtools/theme/layouts/delete?layout='.$layoutId, true,
                    '~devtools/theme/layouts/'
                ),
                $this->_('Delete layout')
            )
            ->setIcon('delete'),

        '|',

        $this['menuEntries'],

        '|',

        $this->html->link(
                '~devtools/theme/layouts/details?layout='.$layoutId,
                $this->_('Details'),
                true
            )
            ->setIcon('details')
            ->setDisposition('informative'),

        $this->html->link(
                '~devtools/theme/layouts/slots?layout='.$layoutId,
                $this->_('Slots'),
                true
            )
            ->setNote($this->format->counterNote($this['slotCount']))
            ->setIcon('list')
            ->setDisposition('informative'),

        '|',

        $this->html->backLink()
    );