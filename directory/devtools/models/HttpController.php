<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\opal;

class HttpController extends arch\Controller {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');
        $probe = new axis\introspector\Probe($this->application);
        $view['unitList'] = $probe->probeUnits();

        return $view;
    }

    public function unitDetailsHtmlAction() {
        $view = $this->aura->getView('UnitDetails.html');
        $this->fetchUnit($view);

        return $view;
    }


// Cache
    public function cacheStatsHtmlAction() {
        $view = $this->aura->getView('CacheStats.html');
        $this->fetchUnit($view, 'cache');

        $view['stats'] = $view['unit']->getUnit()->getCacheStats();
        return $view;
    }


// Table
    public function tableDataHtmlAction() {
        $view = $this->aura->getView('TableData.html');
        $this->fetchUnit($view, 'table');

        $view['schema'] = $view['unit']->getSchema();
        $primitives = [];

        foreach($view['schema']->getFields() as $name => $field) {
            if($field instanceof opal\schema\INullPrimitiveField) {
                continue;
            }

            $primitive = $field->toPrimitive($view['unit']->getUnit(), $view['schema']);

            if($primitive instanceof opal\schema\IMultiFieldPrimitive) {
                foreach($primitive->getPrimitives() as $primitive) {
                    $primitives[$primitive->getName()] = $primitive;
                }
            } else {
                $primitives[$primitive->getName()] = $primitive;
            }
        }

        $view['primitives'] = $primitives;

        $view['rowList'] = $view['unit']->getUnit()->getUnitAdapter()->getQuerySourceAdapter()->select()
            ->paginate()
                ->setOrderableFields(array_keys($primitives))
                ->applyWith($this->request->query);

        return $view;
    }

    public function backupsHtmlAction() {
        $view = $this->aura->getView('Backups.html');
        $this->fetchUnit($view, 'table');

        $view['backupList'] = $view['unit']->getBackups();

        return $view;
    }

    public function fetchUnit($view, $type=null) {
        $probe = new axis\introspector\Probe($this->application);
        $view['unit'] = $probe->inspectUnit($this->request->query['unit']);

        if($type !== null) {
            if($view['unit']->getType() != $type) {
                $this->throwError(403, 'Unit is not a '.$type);
            }
        }
    }
}