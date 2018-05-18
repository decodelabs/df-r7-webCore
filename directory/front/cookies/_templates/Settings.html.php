<?php
echo $this->html('section.cookie-settings', function () use ($cookieData) {
    yield $this->html('h1', 'Cookie settings');

    yield $this->html('p', 'Please select which types of cookies you consent to - while we may not use cookies that apply in all categories, you may choose your preferences for full peace of mind.');
    yield $this->html('p.note', 'Note, if you decide not to consent to certain types of cookies, any 3rd party services that rely on them will not be loaded and may result in a degraded experience.');

    $form = $this->html->form();

    $form->addField('Necessary cookies')->push(
        $this->html->checkbox('necessary', true, 'Cookies that help make the website usable - the website cannot function properly without these')
            ->isDisabled(true)
    );

    $form->addField('Preferences or functionality cookies')->push(
        $this->html->checkbox('preferences', $cookieData['preferences'], 'Cookies that allow the website to adjust it\'s behaviour or presentation according to your choices')
    );

    $form->addField('Statistics or analytics cookies')->push(
        $this->html->checkbox('statistics', $cookieData['statistics'], 'Cookies that anonymously track how you use the website to allow us to improve your experience')
    );

    $form->addField('Marketing or targeted cookies')->push(
        $this->html->checkbox('marketing', $cookieData['marketing'], 'Cookies that are used to track visitors across websites to enable engaging ads and content')
    );

    $form->addButtonArea(
        $this->html->submitButton('save', 'Save settings')
    );

    yield $form;
});
