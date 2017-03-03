<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\opal;

class HttpTableData extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('TableData.html');

        $view['unit'] = (new axis\introspector\Probe())
            ->inspectUnit($this->request['unit']);

        if($view['unit']->getType() != 'table') {
            throw core\Error::{'EForbidden'}([
                'message' => 'Unit is not a table',
                'http' => 403
            ]);
        }

        $view['schema'] = $view['unit']->getTransientSchema();
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

        if($view['unit']->storageExists()) {
            $view['rowList'] = $view['unit']->getUnit()->getUnitAdapter()->getQuerySourceAdapter()->select()
                ->paginate()
                    ->setOrderableFields(...array_keys($primitives))
                    ->applyWith($this->request->query);
        }

        return $view;
    }
}