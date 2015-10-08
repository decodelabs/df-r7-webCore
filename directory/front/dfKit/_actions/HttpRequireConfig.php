<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\dfKit\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpRequireConfig extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs() {
        $themeId = $this->request->query['theme'];

        if(!$themeId) {
            $config = aura\theme\Config::getInstance();
            $themeId = $config->getThemeIdFor('front');
        }

        try {
            $theme = aura\theme\Base::factory($themeId);
        } catch(aura\theme\IException $e) {
            $this->throwError(404, 'Theme not found');
        }

        $manager = aura\theme\Manager::getInstance();
        $dependencies = $manager->getInstalledDependenciesFor($theme);

        $paths = $shims = [];

        foreach($dependencies as $key => $dependency) {
            if(empty($dependency->js)) {
                continue;
            }

            $js = $dependency->js;
            $main = array_shift($js);

            if(substr($main, -3) == '.js') {
                $main = substr($main, 0, -3);
            }

            $paths[$dependency->id] = 'vendor/'.$dependency->installName.'/'.$main;

            if(isset($dependency->shim)) {
                $shims[$dependency->id] = $dependency->shim;
            }
        }

        $data = ['paths' => $paths];

        if(!empty($shims)) {
            $data['shims'] = $shims;
        }

        $output = 'define(function() { return '.str_replace('\\/', '/', json_encode($data)).'; });';
        $output = $this->http->stringResponse($output, 'text/javascript');

        $output->headers
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+1 year');

        return $output;
    }
}