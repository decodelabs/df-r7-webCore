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

use DecodeLabs\Tagged as Html;

class VideoJs extends arch\Helper implements arch\IDirectoryHelper, aura\view\IImplicitViewHelper
{
    use aura\view\TView_DirectoryHelper;

    protected static $_instanceId = 0;

    public function __invoke($embed, array $attributes=null)
    {
        $embed = trim($embed);

        if (empty($embed)) {
            return '';
        }

        $width = $attributes['width'] ?? null;
        $height = $attributes['height'] ?? null;
        unset($attributes['width'], $attributes['height']);

        if (!$embed = Html::$embed->video($embed, $width, $height)) {
            return;
        }

        $isFront = $this->request->isArea('front');

        if (!$isFront) {
            return $embed->render();
        }

        $isHtmlView = $this->view instanceof aura\view\IHtmlView;

        if ($isHtmlView) {
            $this->view->linkCss('dependency://videojs/dist/video-js.min.css', 1000);
        }

        if (isset($attributes['dfKit'])) {
            $xSetup = true;

            if ($isHtmlView) {
                $this->view->dfKit->load($attributes['dfKit']);
            }

            unset($attributes['dfKit']);
        } else {
            $xSetup = false;

            if ($isHtmlView) {
                $this->view->dfKit->load(
                    'vendor-static/Vimeo',
                    'videojs-youtube',
                    'videojs'
                );
            }
        }

        $url = $embed->getPreparedUrl();
        $provider = $embed->getProvider();
        $elementId = 'videoJs'.self::$_instanceId++.'-'.time();
        $setup = [];
        $sources = null;
        $poster = false;


        if (isset($attributes['autoplay'])) {
            $embed->shouldAutoPlay((bool)$attributes['autoplay']);
            unset($attributes['autoplay']);
        }

        $setup['autoplay'] = $embed->shouldAutoPlay();

        switch ($provider) {
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
            'id' => $elementId,
            'controls' => true,
            'preload' => 'auto',
            'width' => $embed->getWidth(),
            'height' => $embed->getHeight(),
            ($xSetup ? 'data-x-setup' : 'data-setup') => flex\Json::toString($setup),
            'poster' => $poster,
            'autoplay' => $embed->shouldAutoPlay()
        ]);

        if (isset($attributes['skin'])) {
            $skin = $attributes['skin'];
            unset($attributes['skin']);
        } else {
            $skin = 'vjs-default-skin';
        }

        if ($attributes) {
            $output->addAttributes($attributes);
        }

        $output->addClass('video-js video '.$skin);
        return $output;
    }

    public function loadResources()
    {
        $this->view->linkCss('dependency://videojs/dist/video-js.min.css', 1000);

        $this->view->dfKit->load(
            'vendor-static/Vimeo',
            'videojs-youtube',
            'videojs'
        );
    }
}
