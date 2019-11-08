<?php
echo $generator->document($this->view->getSubject(), function () use ($generator) {
    yield $generator->previewText($this['previewText']);

    yield $generator->contentArea(function () use ($generator) {
        yield $generator->section(
            Html::raw($this->renderInnerContent())
        );
    });

    yield $generator->footer(function () use ($generator) {
        yield 'Email sent by ';

        $url = $this->uri('/');
        yield $generator->link($url, $url->toReadableString());
    });
});
