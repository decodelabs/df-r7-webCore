<?php
require_once dirname(__DIR__).'/vendor/df-r7/base/tests/bootstrap.php';
require_once dirname(__DIR__).'/Package.php';

df\Launchpad::$loader->loadPackages([
    'webCore'
]);
