<?php

use DecodeLabs\Dictum;
use DecodeLabs\Tagged as Html;

echo $this->apex->component('IndexHeaderBar');


if (!$config->isEnabled()) {
    echo $this->html->flashMessage($this->_(
        'GeoIP lookup is currently disabled in config'
    ), 'warning');
}

echo Html::{'h3'}($this->_('Adapters'));

echo $this->html->attributeList([])

    // Default
    ->addField('defaultAdapter', function () use ($config, $adapterList) {
        $name = $config->getDefaultAdapter();
        $output = Dictum::name($name);
        $available = isset($adapterList[$name]) && $adapterList[$name];
        return Html::{'span'}($output)->addClass($available ? 'positive' : 'negative');
    })

    // Available
    ->addField('availableAdapters', function () use ($adapterList) {
        return Html::uList($adapterList, function ($available, $el, $key) {
            $name = Dictum::name($key);
            return Html::{'span'}($name)->addClass($available ? 'positive' : 'negative');
        });
    });


echo Html::{'h3'}($this->_('My IP details'));

if ($result->ip->isLoopback()) {
    echo $this->html->flashMessage($this->_(
        'You are currently browsing this site on the server\'s local network, your internet IP is not available for lookup'
    ), 'warning');
}

echo $this->html->attributeList($result)
    // IP
    ->addField('myIp', function ($result) {
        return $result->ip;
    })

    // Continent
    ->addField('continent', function ($result) {
        return $result->continentName;
    })

    // Country
    ->addField('country', function ($result) {
        return $result->countryName;
    })

    // Region
    ->addField('region', function ($result) {
        return $result->regionName;
    })

    // City
    ->addField('city', function ($result) {
        return $result->cityName;
    })

    // Postcode
    ->addField('postcode', function ($result) {
        return $result->postcode;
    })

    // Latlong
    ->addField('latLong', $this->_('Coordinates'), function ($result) {
        if ($result->latitude) {
            return $result->latitude . ' / ' . $result->longitude;
        }
    });
