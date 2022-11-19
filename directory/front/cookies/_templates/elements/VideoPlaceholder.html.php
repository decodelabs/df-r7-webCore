<?php

use DecodeLabs\Tagged as Html;

echo Html::{'div.video-cookie-placeholder > div.video-message'}(function () {
    $category = (array)($this['categories'] ?? 'statistics');
    $category = Html::iList($category, null, ', ', ' and ');

    yield Html::{'p.strong'}(['This video is hosted on a service that uses ', $category, 'tracking cookies.']);
    yield Html::{'p'}([
        'These cookies are currently disabled - to view this video, you will need to consent to and re-enable ',
        $category, ' cookies in your ',
        $this->html->link('cookies/settings', 'Cookie Settings')
            ->addClass('modal')
            ->setIcon('settings')
    ]);

    yield $this->html->link('cookies/dismiss-notice', 'Enable all cookies')
        ->addClass('btn')
        ->setIcon('tick');
});
