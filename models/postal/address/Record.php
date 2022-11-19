<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\postal\address;

use df\opal;
use df\user;

class Record extends opal\record\Base implements user\IPostalAddress
{
    use user\TPostalAddress;

    public function getStreetLine1()
    {
        return $this['street1'];
    }

    public function getStreetLine2()
    {
        return $this['street2'];
    }

    public function getStreetLine3()
    {
        return $this['street3'];
    }

    public function getLocality()
    {
        return $this['city'];
    }

    public function getRegion()
    {
        return $this['county'];
    }

    public function getPostalCode()
    {
        return $this['postcode'];
    }

    public function getCountryCode()
    {
        return $this['country'];
    }

    public function mustSpawnToEdit($requestedAccess, $clientId = null)
    {
        if ($clientId === null) {
            $clientId = user\Manager::getInstance()->getClient()->getId();
        }

        $access = $this['access'];
        $ownerId = $this->getRaw('owner');

        if ($requestedAccess == 'private'
        && ($access != 'private' || $ownerId != $clientId)) {
            return true;
        }

        if ($access == 'private'
        && ($requestedAccess != 'private' || $ownerId != $clientId)) {
            return true;
        }

        if ($access == 'protected' && $requestedAccess == 'public') {
            return true;
        }

        return false;
    }

    public function mustSpawnOnSave($requestedAccess = null)
    {
        $access = $this['access'];

        if ($this->isNew() || $access != 'public' || !$this->hasChanged()) {
            return false;
        }

        if ($requestedAccess == 'protected') {
            return true;
        }

        if ($this->countChanges() > 2) {
            return true;
        }

        $current = $this['street1'] . $this['street2'] . $this['street3'] . $this['city'] .
                   $this['county'] . $this['postcode'] . $this['country'];

        $original = $this->getOriginal('street1') .
                    $this->getOriginal('street2') .
                    $this->getOriginal('street3') .
                    $this->getOriginal('city') .
                    $this->getOriginal('county') .
                    $this->getOriginal('postcode') .
                    $this->getOriginal('country');

        if (levenshtein($current, $original) > 10) {
            return true;
        }

        return false;
    }

    public function dereferenceDuplicate()
    {
        $currentAccess = $this['access'];

        // Private cannot be shared so no need to dereference
        if ($currentAccess == 'private') {
            return $this;
        }

        // If it's not new then assume being shared, must be public
        if (!$this->isNew()) {
            $currentAccess = 'public';
            $this->save();
            return $this;
        }

        // Lookup duplicate
        $access = [$currentAccess];

        if ($currentAccess == 'protected') {
            $access[] = 'public';
        }

        $adapter = $this->getAdapter();
        $output = $adapter->fetch()
            ->where('street1', '=', $this['street1'])
            ->where('street2', '=', $this['street2'])
            ->where('street3', '=', $this['street3'])
            //->where('city', '=', $this['city'])
            //->where('county', '=', $this['county'])
            ->where('postcode', '=', $this['postcode'])
            ->where('country', '=', $this['country'])
            ->where('access', 'in', $access)
            ->toRow();

        if ($output) {
            // Being shared, must be public
            if ($output['access'] == 'protected') {
                $output['access'] = 'public';
                $output->save();
            }

            return $output;
        }

        return $this;
    }
}
