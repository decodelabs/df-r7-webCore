<?php

use df\core;
use df\flow;

echo $this->html->elementContentContainer(function() use($message) {
    $renderer = function(array $parts, $baseId='') use(&$renderer) {
        foreach($parts as $i => $part) {
            $currId = $baseId.$i;

            if($part instanceof flow\mime\IMultiPart) {
                yield $this->html->container(
                    $this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em'),
                    $renderer($part->getParts(), $currId.'-')
                );
            } else if($part instanceof flow\mime\IContentPart) {
                yield $this->html->container(function() use($part, $currId) {
                    yield $this->html->attributeList(
                            $part->getHeaders()->toArray() +
                            [
                                'download' => $this->html->link('./download?mail='.$this['mail']['id'].'&part='.$currId, 'Download part')
                                    ->setIcon('download')
                            ]
                        )
                        ->setStyle('font-size', '0.8em');

                    switch($part->getContentType()) {
                        case 'text/plain':
                            yield $this->html('div.sterile', $this->html->plainText($part->getContent()));
                            break;

                        case 'text/html':
                            $doc = core\xml\Tree::fromHtmlString($html = $part->getContent());
                            $attr = [];

                            if($body = $doc->getFirstChildOfType('body')) {
                                $body->setTagName('div');
                                $html = $body->toNodeXmlString();
                            }

                            yield $this->html('div.sterile', $this->html->string($html));
                            break;
                    }
                });
            }
        }
    };

    return $renderer([$message]);
});