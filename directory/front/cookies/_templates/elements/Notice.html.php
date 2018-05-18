<?php
echo $this->html('div#cookieNotice > div.cookie-message', function () {
    yield $this->html('p', 'We use cookies on this website to maintain your browsing session and to improve the ways you use it.');

    yield $this->html('p', [
        'You can choose what types of cookies you consent to on this site via your ',
        $this->html->link('cookies/settings', 'Cookie Settings')
            ->addClass('modal')
            ->setIcon('settings'),
        '.'
    ]);

    yield $this->html('p', [
        'Otherwise, if you\'re happy to consent to ', $this->html('strong', 'all cookies we use'), ' you can ',
        $this->html->link($this->uri('cookies/dismiss-notice', true), 'accept and carry on')
            ->setRelationship('nofollow')
            ->setIcon('tick'),
        '.'
    ]);

    yield $this->html('p.note', 'You can modify your cookie settings at any time via the Cookie Settings link at the bottoms of the page.');

    if ($privacy = $this['privacyRequest']) {
        yield $this->html('p.note', [
            'More information about how we handle personal data can be found in our ',
            $this->html->link($privacy, 'Privacy Policy'),
            '.'
        ]);
    }
});
