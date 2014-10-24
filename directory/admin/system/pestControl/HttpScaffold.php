<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'Pest control';
    const DIRECTORY_ICON = 'bug';

    public function generateIndexMenu($entryList) {
        $criticalErrorCount = $this->data->pestControl->errorLog->select()->count();
        $notFoundCount = $this->data->pestControl->missLog->select()->count();
        $accessErrorCount = $this->data->pestControl->accessLog->select()->count();

        $entryList->addEntries(
            $entryList->newLink('~admin/system/pestControl/errors/', 'Critical errors')
                ->setId('errors')
                ->setDescription('Get detailed information on critical errors encountered by users')
                ->setIcon('error')
                ->setNote($this->format->counterNote($criticalErrorCount))
                ->setWeight(10),

            $entryList->newLink('~admin/system/pestControl/misses/', '404 errors')
                ->setId('misses')
                ->setDescription('View requests that users are making to files and actions that don\'t exist')
                ->setIcon('brokenLink')
                ->setNote($this->format->counterNote($notFoundCount))
                ->setWeight(20),

            $entryList->newLink('~admin/system/pestControl/access/', 'Access errors')
                ->setId('access')
                ->setDescription('See who is trying to access things they are not supposed to')
                ->setIcon('lock')
                ->setNote($this->format->counterNote($accessErrorCount))
                ->setWeight(30)
        );
    }


    public function importAction() {
        $access = $critical = $misses = 0;

        foreach($this->data->log->accessError->select()->orderBy('date ASC') as $orig) {
            $new = $this->data->pestControl->accessLog->newRecord([
                    'date' => $orig['date'],
                    'mode' => $orig['mode'],
                    'code' => $orig['code'],
                    'request' => $orig['request'],
                    'message' => $orig['message'],
                    'userAgent' => $this->data->user->agent->logAgent($orig['userAgent']),
                    'userId' => (string)$orig['user'],
                    'isProduction' => $orig['isProduction']
                ]);

            $new->save();
            $access++;
        }

        foreach($this->data->log->criticalError->select()->orderBy('date ASC') as $orig) {
            $error = $this->data->pestControl->error->logError(
                $orig['exceptionType'],
                null,
                $orig['file'],
                $orig['line'],
                $orig['message']
            );

            if($error['seen'] == 1) {
                $error['firstSeen'] = clone $orig['date'];
            }

            $error['lastSeen'] = clone $orig['date'];
            $error->save();

            $new = $this->data->pestControl->errorLog->newRecord([
                    'date' => $orig['date'],
                    'error' => $error,
                    'mode' => $orig['mode'],
                    'request' => $orig['request'],
                    'message' => null,
                    'userAgent' => $this->data->user->agent->logAgent($orig['userAgent']),
                    'stackTrace' => $this->data->pestControl->stackTrace->logJson($orig['stackTrace']),
                    'userId' => (string)$orig['user'],
                    'isProduction' => $orig['isProduction']
                ]);

            $new->save();
            $critical++;
        }

        foreach($this->data->log->notFound->select()->orderBy('date ASC') as $orig) {
            $miss = $this->data->pestControl->miss->logMiss(
                $orig['request'],
                $orig['mode']
            );

            if($miss['seen'] == 1) {
                $miss['firstSeen'] = clone $orig['date'];
            }

            $miss['lastSeen'] = clone $orig['date'];
            $miss->save();

            $new = $this->data->pestControl->missLog->newRecord([
                    'date' => $orig['date'],
                    'miss' => $miss,
                    'referrer' => $orig['referrer'],
                    'message' => $orig['message'],
                    'userId' => (string)$orig['user'],
                    'isProduction' => $orig['isProduction']
                ]);

            $new->save();
            $misses++;
        }

        core\dump('Imported '.$access.' access logs, '.$critical.' error logs and '.$miss.' 404 logs');
    }
}