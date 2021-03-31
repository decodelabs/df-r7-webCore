<?php
use DecodeLabs\Tagged as Html;

echo Html::{'div#cookieNotice > div.cookie-message'}(function () {
    yield Html::{'p'}('We use cookies on this website to maintain your browsing session and to improve the ways you use it.');

    yield Html::{'p'}([
        'You can choose what types of cookies you consent to on this site via your ',
        $this->html->link('cookies/settings', 'Cookie Settings')
            ->addClass('modal')
            ->setIcon('settings'),
        '.'
    ]);

    yield Html::{'p'}([
        'Otherwise, if you\'re happy to consent to ', Html::{'strong'}('all cookies we use'), ' you can ',
        $this->html->link($this->uri('cookies/dismiss-notice', true), 'accept and carry on')
            ->setRelationship('nofollow')
            ->setIcon('tick'),
        '.'
    ]);

    yield Html::{'p.note'}('You can modify your cookie settings at any time via the Cookie Settings link at the bottoms of the page.');

    if ($privacy = $this['privacyRequest']) {
        yield Html::{'p.note'}([
            'More information about how we handle personal data can be found in our ',
            $this->html->link($privacy, 'Privacy Policy'),
            '.'
        ]);
    }
});
