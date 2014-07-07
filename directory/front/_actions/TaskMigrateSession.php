<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

class TaskMigrateSession extends arch\task\Action {
    
    public function execute() {
        try {
            $this->data->session->manifest->getUnitAdapter()->getQuerySourceAdapter()->truncate();
            $this->response->writeLine('Emptied manifest table');
        } catch(\Exception $e) {}

        try {
            $this->data->session->data->getUnitAdapter()->getQuerySourceAdapter()->truncate();
            $this->response->writeLine('Emptied data table');
        } catch(\Exception $e) {}


        $insert = $this->data->session->manifest->batchInsert();

        foreach($this->data->user->sessionManifest->select() as $row) {
            $insert->addRow($row);
        }

        $count = $insert->execute();
        $this->response->writeLine('Copied '.$count.' manifest rows');


        $insert = $this->data->session->data->batchInsert();

        foreach($this->data->user->sessionData->select() as $row) {
            $insert->addRow($row);
        }

        $count = $insert->execute();
        $this->response->writeLine('Copied '.$count.' data rows');


        if(df\Launchpad::$environmentId == 'dl') {
            $this->runChild('application/build');
        }
    }
}