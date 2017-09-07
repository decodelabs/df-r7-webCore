<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\helpers;

use df;
use df\core;
use df\apex;
use df\aura;
use df\arch;
use df\spur;
use df\flex;
use df\link;

class MediaElement extends arch\Helper implements arch\IDirectoryHelper, aura\view\IImplicitViewHelper {

    use aura\view\TView_DirectoryHelper;

    public function __invoke(string $type, ?string $embed, array $attributes=null) {
        if($type == 'audio') {
            return $this->audio($embed, $attributes);
        } else if($type == 'video') {
            return $this->video($embed, $attributes);
        } else {
            throw core\Error::EArgument('Invalid media element type: '.$type);
        }
    }


// Audio
    public function audio(?string $embed, array $attributes=null) {
        $embed = trim($embed);

        if(empty($embed)) {
            return null;
        }

        $embed = $this->html->audioEmbed($embed, 940);

        if($embed->getProvider() == 'audioboom') {
            // Audioboom
            $url = link\http\Url::factory($embed->getUrl());
            $booId = $url->path->get(1);
            $sourceUrl = 'https://audioboom.com/posts/'.$booId.'.mp3';
            $type = 'audio/mp3';
        } else {
            // Don't know??
            return $embed;
        }

        $this->view->dfKit->load('lib/df-kit/mediaelement');

        if(isset($attributes['dfKit'])) {
            $this->view->dfKit->load($attributes['dfKit']);
            unset($attributes['dfKit']);
        } else {
            $attributes['data-mejs'] = '';
        }

        return $this->html('div.container.mejs.audio > audio.w.embed', null, array_merge($attributes ?? [], [
            'type' => $type,
            'src' => $sourceUrl,
            'controls' => true,
            'preload' => 'auto'
        ]));
    }



// Video
    public function video(?string $embed, array $attributes=null) {
        $embed = trim($embed);

        if(empty($embed)) {
            return null;
        }

        $width = $attributes['width'] ?? null;
        $height = $attributes['height'] ?? null;
        unset($attributes['width'], $attributes['height']);

        $embed = spur\video\Embed::parse($embed)
            ->setDimensions($width, $height);


        if(!$this->request->isArea('front')) {
            return $embed->render();
        }

        $this->view->dfKit->load('df-kit/mediaelement');
        $sourceUrl = $embed->getPreparedUrl();

        // DF Kit
        if(isset($attributes['dfKit'])) {
            $this->view->dfKit->load($attributes['dfKit']);
            unset($attributes['dfKit']);
        } else {
            $attributes['data-mejs'] = '';
        }

        // Autoplay
        if(isset($attributes['autoplay'])) {
            $embed->shouldAutoPlay((bool)$attributes['autoplay']);
            unset($attributes['autoplay']);
        }

        // Provider
        $attributes['data-provider'] = $provider = $embed->getProvider();

        if($provider == 'vimeo') {
            $sourceUrl .= '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0';
        }

        return $this->html('div.container.mejs.video > video.w.embed', null, array_merge($attributes ?? [], [
            //'type' => $type,
            'src' => $sourceUrl,
            'controls' => true,
            'preload' => 'auto'
        ]));
    }
}
