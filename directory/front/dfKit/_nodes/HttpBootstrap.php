<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\dfKit\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpBootstrap extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs() {
        $theme = $this->_getTheme();
        $data = $this->_getRequireConfig($theme);

        $output =
            'if(typeof require == \'undefined\') { throw new Error(\'Require.js has not been loaded\'); };'."\n".
            'define(\'require.config\', function() { return '.str_replace('\\/', '/', json_encode($data)).'; });'."\n";

        $output .= file_get_contents(__DIR__.'/bootstrap.js');

        $output = $this->http->stringResponse($output, 'text/javascript');
        $output->headers
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+1 year');

        return $output;
    }

    protected function _getTheme() {
        $themeId = $this->request['theme'];

        if(!$themeId) {
            $config = aura\theme\Config::getInstance();
            $themeId = $config->getThemeIdFor('front');
        }

        try {
            $theme = aura\theme\Base::factory($themeId);
        } catch(aura\theme\IException $e) {
            $this->throwError(404, 'Theme not found');
        }

        return $theme;
    }

    protected function _getRequireConfig($theme) {
        $manager = aura\theme\Manager::getInstance();
        $dependencies = $manager->getInstalledDependenciesFor($theme);

        $paths = $shims = $maps = [];

        foreach($dependencies as $key => $dependency) {
            if(isset($dependency->map)) {
                $maps = array_merge($maps, $dependency->map);
            }

            if(empty($dependency->js)) {
                continue;
            }

            $js = $dependency->js;
            $main = array_shift($js);

            if(substr($main, -3) == '.js') {
                $main = substr($main, 0, -3);
            }

            $paths['{'.$dependency->id.'}'] = 'vendor/'.$dependency->installName;
            $paths[$dependency->id] = 'vendor/'.$dependency->installName.'/'.$main;

            if(isset($dependency->shim)) {
                $shims[$dependency->id] = $dependency->shim;
            }
        }

        $data = ['paths' => $paths];

        if(!empty($shims)) {
            $data['shims'] = $shims;
        }

        if(!empty($maps)) {
            $data['map'] = $maps;
        }

        return $data;
    }
}