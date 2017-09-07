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

    public function audio(?string $embed, array $attributes=null) {
        $embed = trim($embed);

        if(empty($embed)) {
            return null;
        }

        $embed = $this->html->audioEmbed($embed, 940);
        $sourceUrl = null;

        if($embed->getProvider() == 'audioboom') {
            // Audioboom
            $url = link\http\Url::factory($embed->getUrl());
            $booId = $url->path->get(1);
            $sourceUrl = 'https://audioboom.com/posts/'.$booId.'.mp3';
        }


        if($sourceUrl !== null) {
            $this->view->dfKit->load('lib/df-kit/mediaelement');

            return $this->html('div.container.mejs.audio > audio.w.embed', null, [
                'type' => 'audio/mp3',
                'src' => $sourceUrl,
                'controls' => true,
                'preload' => 'auto'
            ]);
        }

        return $embed;
    }

    /*
    public function video(?string $embed, array $attributes=null) {
        $embed = trim($embed);

        if(empty($embed)) {
            return null;
        }
    }
    */
}
