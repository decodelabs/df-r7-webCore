<?php
use DecodeLabs\Tagged\Html;

echo Html::h1(['Error ', $code]);
echo Html::p($message);
