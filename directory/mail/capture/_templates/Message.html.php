<?php

use df\core;
use df\flow;

echo $this->html->elementContentContainer(function() use($message) {
    $renderer = function(array $parts) use(&$renderer) {
        foreach($parts as $part) {
            if($part instanceof flow\mime\IMultiPart) {
                yield $this->html->container(
                    $this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em'),
                    $renderer($part->getParts())
                );
            } else if($part instanceof flow\mime\IContentPart) {
                $content = [$this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em')];

                switch($part->getContentType()) {
                    case 'text/plain':
                        $content[] = $this->html('div.sterile', $this->html->plainText($part->getContent()));
                        break;

                    case 'text/html':
                        $content[] = $this->html('div.sterile', $this->html->string($part->getContent()));
                        break;
                }

                yield $this->html->container($content);
            }
        }
    };

    return $renderer([$message]);
});