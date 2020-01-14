<?php
$category = (array)($this['categories'] ?? 'statistics');
$category = Html::iList($category, null, ', ', ' and ');
?>
<div class="audio-cookie-placeholder">
    <div class="audio-message">
        <p class="strong">This audio is hosted on a service that uses <?php echo $category; ?> tracking cookies.</p>
        <p>These cookies are currently disabled - to listen to this audio, you will need to consent to and re-enable <?php echo $category; ?> cookies in your
        <?php echo $this->html->link('cookies/settings', 'Cookie Settings')->addClass('modal')->setIcon('settings'); ?>.</p>
    </div>
</div>


<?php

echo Html::{'div.audio-cookie-placeholder > div.audio-message'}(function () {
    $category = (array)($this['categories'] ?? 'statistics');
    $category = Html::iList($category, null, ', ', ' and ');

    yield Html::{'p.strong'}(['This audio is hosted on a service that uses ', $category, 'tracking cookies.']);
    yield Html::{'p'}([
        'These cookies are currently disabled - to listen to this audio, you will need to consent to and re-enable ',
        $category, ' cookies in your ',
        $this->html->link('cookies/settings', 'Cookie Settings')
            ->addClass('modal')
            ->setIcon('settings')
    ]);

    yield $this->html->link('cookies/dismiss-notice', 'Enable all cookies')
        ->addClass('btn')
        ->setIcon('tick');
});
