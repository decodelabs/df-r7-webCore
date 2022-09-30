<?php

use DecodeLabs\R7\Legacy;

require_once dirname(__DIR__).'/vendor/df-r7/base/tests/bootstrap.php';
require_once dirname(__DIR__).'/Package.php';

Legacy::getLoader()->loadPackages([
    'webCore'
]);
