<?php

echo $this->import->component('UnitDetailHeaderBar', '~devtools/models/', $this['unit']);

echo $this->html->element('h2', $this->getArg('title', $this->_('Task results')));

echo $this->import->template('elements/TaskResult.html', '~shared/');