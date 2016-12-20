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

class VideoJs extends arch\Helper implements arch\IDirectoryHelper, aura\view\IImplicitViewHelper {

    use aura\view\TView_DirectoryHelper;

    protected static $_instanceId = 0;

    public function __invoke($embed, array $attributes=null) {
        $embed = trim($embed);

        if(empty($embed)) {
            return '';
        }

        $width = $attributes['width'] ?? null;
        $height = $attributes['height'] ?? null;
        unset($attributes['width'], $attributes['height']);

        $embed = spur\video\Embed::parse($embed)
            ->setDimensions($width, $height);

        $isFront = $this->request->isArea('front');

        if(!$isFront) {
            return $embed->render();
        }

        $this->view->linkCss('dependency://videojs/dist/video-js.min.css', 1000);

        if(isset($attributes['dfKit'])) {
            $xSetup = true;
            $this->view->dfKit->load($attributes['dfKit']);
            unset($attributes['dfKit']);
        } else {
            $xSetup = false;
            $this->view->dfKit->load(
                'vendor-static/Vimeo',
                'videojs-youtube',
                'videojs'
            );
        }

        $url = $embed->getPreparedUrl();
        $provider = $embed->getProvider();
        $id = 'videoJs'.self::$_instanceId++;
        $setup = [];
        $sources = null;
        $poster = false;


        if(isset($attributes['autoplay'])) {
            $embed->shouldAutoPlay((bool)$attributes['autoplay']);
            unset($attributes['autoplay']);
        }

        $setup['autoplay'] = $embed->shouldAutoPlay();

        switch($provider) {
            case 'youtube':
                $this->_youtube = true;
                $setup['techOrder'] = ['youtube'];
                $setup['sources'] = [[
                    'type' => 'video/youtube',
                    'src' => (string)$url,
                    'quality' => '1080p'
                ]];
                break;

            case 'vimeo':
                $this->_vimeo = true;
                $setup['techOrder'] = ['vimeo'];
                $id = $url->getPath()->getLast();

                $setup['sources'] = [[
                    'type' => 'video/vimeo',
                    'src' => 'https://vimeo.com/'.$id
                ]];
                break;
        }


        $output = new aura\html\Element('video', $sources, [
            'id' => $id,
            'controls' => true,
            'preload' => 'auto',
            'width' => $embed->getWidth(),
            'height' => $embed->getHeight(),
            ($xSetup ? 'data-x-setup' : 'data-setup') => flex\json\Codec::encode($setup),
            'poster' => $poster,
            'autoplay' => $embed->shouldAutoPlay()
        ]);

        if(isset($attributes['skin'])) {
            $skin = $attributes['skin'];
            unset($attributes['skin']);
        } else {
            $skin = 'vjs-default-skin';
        }

        if($attributes) {
            $output->addAttributes($attributes);
        }

        $output->addClass('video-js '.$skin);
        return $output;
    }
}