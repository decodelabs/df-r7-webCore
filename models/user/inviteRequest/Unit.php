<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\inviteRequest;

use df;
use df\core;
use df\apex;
use df\axis;
    
class Unit extends axis\unit\table\Base {

    protected $_defaultSearchFields = [
        'name' => 10,
        'email' => 2,
        'companyName' => 5,
        'companyPosition' => 1
    ];

    protected $_defaultOrderableFields = [
        'name', 'email', 'companyName', 'companyPosition', 'creationDate', 'isActive'
    ];

    protected $_defaultOrder = 'creationDate DESC';

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addPrimaryField('id', 'AutoId', 8);

        $schema->addField('name', 'String', 128);
        $schema->addField('email', 'String', 255);

        $schema->addField('companyName', 'String', 255)
            ->isNullable(true);
        $schema->addField('companyPosition', 'String', 255)
            ->isNullable(true);

        $schema->addField('message', 'BigString', 'medium')
            ->isNullable(true);

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('isActive', 'Boolean')
            ->setDefaultValue(true);

        $schema->addField('invite', 'One', 'user/Invite')
            ->isNullable(true);
    }
}