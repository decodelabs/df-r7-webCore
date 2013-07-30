<?php
use df\core;

echo $this->import->component('DetailHeaderBar', '~devtools/mail/dev/', $this['mail']);

echo $this->html->flashMessage($this->_(
    'This message was received %t% ago',
    ['%t%' => $this->format->timeSince($this['mail']['date'])]
));

if($this['mail']['isPrivate']) {
    echo $this->html->flashMessage($this->_(
        'This message is marked as private'
    ), 'warning');
}


echo $this->html->elementContentContainer(function() {
    $renderer = function(array $parts) use(&$renderer) {
        $output = [];

        foreach($parts as $part) {
            if($part instanceof core\mime\IMultiPart) {
                $output[] = $this->html->container(
                    $this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em'),
                    $renderer($part->getParts())
                );
            } else if($part instanceof core\mime\IContentPart) {
                $content = [$this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em')];

                switch($part->getContentType()) {
                    case 'text/plain':
                        $content[] = $this->html->element('div', $this->html->plainText($part->getContent()));
                        break;

                    case 'text/html':
                        $content[] = $this->html->element('div', $this->html->string($part->getContent()));
                        break;
                }

                $output[] = $this->html->container($content);
            }
        }        

        return $output;
    };

    return $renderer([$this['message']]);
});