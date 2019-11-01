<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\postal\address;

use df;
use df\core;
use df\apex;
use df\axis;
use df\user;
use df\flex;

class Unit extends axis\unit\Table {

    protected function createSchema($schema) {
        $schema->addPrimaryField('id', 'Guid');

        $schema->addField('street1', 'Text', 128);
        $schema->addField('street2', 'Text', 128)->isNullable(true);
        $schema->addField('street3', 'Text', 128)->isNullable(true);
        $schema->addField('city', 'Text', 128);
        $schema->addField('county', 'Text', 64)->isNullable(true);
        $schema->addField('country', 'Text', 2, flex\ICase::UPPER);
        $schema->addField('postcode', 'Text', 12);

        $schema->addField('owner', 'One', 'user/client');
        $schema->addField('access', 'Enum', ['public', 'protected', 'private'])
            ->setDefaultValue('protected');

        $schema->addIndex('country');
        $schema->addIndex('access');
    }


    public function lookupPostcode($search, $access=null) {
        if($access === null) {
            $access = 'protected';
        }

        $clientId = $this->context->user->client->getId();

        $output = $this->fetch()
            ->where('postcode', 'like', $search)
            ->limit(100);

        switch($access) {
            case 'public':
                $output->beginWhereClause()
                    ->where('access', '=', 'public')
                    ->beginOrWhereClause()
                        ->where('access', '=', 'private')
                        ->where('owner', '=', $clientId)
                        ->endClause()
                    ->endClause();
                break;

            case 'protected':
                $output->beginWhereClause()
                    ->where('access', 'in', ['public', 'protected'])
                    ->beginOrWhereClause()
                        ->where('access', '=', 'private')
                        ->where('owner', '=', $clientId)
                        ->endClause()
                    ->endClause();
                break;

            case 'private':
                $output->where('access', '=', 'private')
                    ->where('owner', '=', $clientId);
                break;
        }

        return $output;
    }

    public function lookupPostcodeAsList($search, $access=null) {
        $list = $this->lookupPostcode($search, $access);
        $output = [];

        foreach($list as $address) {
            $output[$address['id']] = $address->toOneLineString();
        }

        return $output;
    }
}