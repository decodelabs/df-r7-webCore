<?php
use df\link;

echo $this->import->component('IndexHeaderBar', '~admin/system/geo-ip/');


if(!$this['config']->isEnabled()) {
    echo $this->html->flashMessage($this->_(
        'GeoIP lookup is currently disabled in config'
    ), 'warning');
}

echo $this->html->element('h3', $this->_('Adapters'));

echo $this->html->attributeList([])
    
    // Default
    ->addField('defaultAdapter', function() {
        $name = $this['config']->getDefaultAdapter();
        $output = $this->format->name($name);
        $available = isset($this['adapterList'][$name]) && $this['adapterList'][$name];
        return $this->html->element('span', $output)->addClass($available ? 'positive' : 'negative');
    })

    // Available
    ->addField('availableAdapters', function() {
        return $this->html->bulletList($this['adapterList'], function($available, $context) {
            $name = $this->format->name($context->getKey());
            return $this->html->element('span', $name)->addClass($available ? 'positive' : 'negative');
        });
    });


echo $this->html->element('h3', $this->_('My IP details'));

if($this['result']->ip->isLoopback()) {
    echo $this->html->flashMessage($this->_(
        'You are currently browsing this site on the server\'s local network, your internet IP is not available for lookup'
    ), 'warning');
}

echo $this->html->attributeList($this['result'])
    // IP
    ->addField('myIp', function($result) {
        return $result->ip;
    })

    // Continent
    ->addField('continent', function($result) {
        return $result->continentName;
    })

    // Country
    ->addField('country', function($result) {
        return $result->countryName;
    })

    // Region
    ->addField('region', function($result) {
        return $result->regionName;
    })

    // City
    ->addField('city', function($result) {
        return $result->cityName;
    })

    // Postcode
    ->addField('postcode', function($result) {
        return $result->postcode;
    })

    // Latlong
    ->addField('latLong', $this->_('Coordinates'), function($result) {
        if($result->latitude) {
            return $result->latitude.' / '.$result->longitude;
        }
    });