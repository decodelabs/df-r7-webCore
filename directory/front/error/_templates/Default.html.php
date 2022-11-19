<?php

use DecodeLabs\Tagged as Html;

echo Html::h1(['Error ', $code]);
echo Html::p($message);
