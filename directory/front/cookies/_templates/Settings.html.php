<?php
echo Html::{'main.cookie-settings'}(function () use ($cookieData) {
    yield Html::{'h1'}('Cookie settings');

    yield Html::{'p'}('Please select which types of cookies you wish to allow - while we may not use cookies that apply in all categories, you may choose your preferences for full peace of mind.');
    yield Html::{'p.note'}('Note, if you decide not to consent to certain types of cookies, any 3rd party services that rely on them will not be loaded and may result in a degraded experience.');

    if ($privacy = $this['privacyRequest']) {
        yield Html::{'p.note'}([
            'For more information about how we handle your data, please see our ',
            $this->html->link($privacy, 'Privacy Policy')->addClass('global')
        ]);
    }


    $form = $this->html->form($this['formRequest'])->addClass($this['isGlobal'] ? 'global' : null);
    $form->addHidden('id', $cookieData['id']);

    $form->addField('Necessary cookies')->push(
        $this->html->checkbox('necessary', true, 'Cookies that help make the website usable - the website cannot function properly without these')
            ->isDisabled(true)
    );

    $form->addField('Preferences or functionality cookies')->push(
        $this->html->checkbox('preferences', $cookieData['preferences'], 'Cookies that allow the website to adjust its behaviour or presentation according to your choices')
            ->isDisabled(!$this['preferences'] && !$cookieData['preferences'])
    )->addClass(!$this['preferences'] ? 'disabled' : null);

    $form->addField('Statistics or analytics cookies')->push(
        $this->html->checkbox('statistics', $cookieData['statistics'], 'Cookies that anonymously track how you use the website to allow us to improve your experience')
            ->isDisabled(!$this['statistics'] && !$cookieData['statistics'])
    )->addClass(!$this['statistics'] ? 'disabled' : null);

    $form->addField('Marketing or targeted cookies')->push(
        $this->html->checkbox('marketing', $cookieData['marketing'], 'Cookies that are used to track visitors across websites to enable engaging ads and content')
            ->isDisabled(!$this['marketing'] && !$cookieData['marketing'])
    )->addClass(!$this['marketing'] ? 'disabled' : null);

    $form->addButtonArea(
        $this->html->submitButton('save', 'Save settings')
    );

    yield $form;
});
