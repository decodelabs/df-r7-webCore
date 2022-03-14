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

use DecodeLabs\Tagged as Html;
use DecodeLabs\Exceptional;

class MediaElement extends arch\Helper implements arch\IDirectoryHelper, aura\view\IImplicitViewHelper
{
    use aura\view\TView_DirectoryHelper;

    public function __invoke(string $type, ?string $embed, array $attributes=null)
    {
        if ($type == 'audio') {
            return $this->audio($embed, $attributes);
        } elseif ($type == 'video') {
            return $this->video($embed, $attributes);
        } else {
            throw Exceptional::InvalidArgument(
                'Invalid media element type: '.$type
            );
        }
    }


    // Audio
    public function audio(?string $embed, array $attributes=null)
    {
        if ($embed === null) {
            return null;
        }

        $embed = trim($embed);

        if (empty($embed)) {
            return null;
        }

        $embed = Html::$embed->audio($embed, 940);

        if ($embed->getProvider() == 'audioboom' && $embed->getAudioboomType() == 'embed') {
            // Audioboom
            $sourceUrl = 'https://audioboom.com/posts/'.$embed->getAudioboomId().'.mp3';
            $type = 'audio/mp3';
        } else {
            // Don't know??
            return $embed;
        }

        $this->view->dfKit->load('lib/df-kit/mediaelement');

        if ($attributes === null) {
            $attributes = [];
        }

        if (isset($attributes['dfKit'])) {
            $this->view->dfKit->load($attributes['dfKit']);
            unset($attributes['dfKit']);
        } else {
            $attributes['data-mejs'] = '';
        }

        return Html::{'div.container.mejs.audio > audio.w.embed'}(null, array_merge($attributes, [
            'type' => $type,
            'src' => $sourceUrl,
            'controls' => true,
            'preload' => 'auto'
        ]));
    }



    // Video
    public function video(?string $embed, array $attributes=null)
    {
        if ($embed === null) {
            return null;
        }

        $embed = trim($embed);

        if (empty($embed)) {
            return null;
        }

        if ($attributes === null) {
            $attributes = [];
        }

        $width = $attributes['width'] ?? null;
        $height = $attributes['height'] ?? null;
        unset($attributes['width'], $attributes['height']);

        if (!$embed = Html::$embed->video($embed, $width, $height)) {
            return null;
        }


        if (!$this->request->isArea('front')) {
            return $embed->render();
        }

        $this->view->dfKit->load('df-kit/mediaelement');
        $sourceUrl = $embed->getPreparedUrl();

        // DF Kit
        if (isset($attributes['dfKit'])) {
            $this->view->dfKit->load($attributes['dfKit']);
            unset($attributes['dfKit']);
        } else {
            $attributes['data-mejs'] = '';
        }

        // Autoplay
        if (isset($attributes['autoplay'])) {
            $embed->shouldAutoPlay((bool)$attributes['autoplay']);
            unset($attributes['autoplay']);
        }

        // Provider
        $attributes['data-provider'] = $provider = $embed->getProvider();

        if ($provider == 'vimeo') {
            $sourceUrl .= '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0';
        }

        return Html::{'div.container.mejs.video > video.w.embed'}(null, array_merge($attributes, [
            //'type' => $type,
            'src' => $sourceUrl,
            'controls' => true,
            'preload' => 'auto'
        ]));
    }
}
