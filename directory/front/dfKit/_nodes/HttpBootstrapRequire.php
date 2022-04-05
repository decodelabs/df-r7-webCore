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
use df\fuse;

use DecodeLabs\Exceptional;

class HttpBootstrapRequire extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs()
    {
        $theme = $this->_getTheme();
        $data = $this->_getRequireConfig($theme);

        $output =
            'if(typeof require == \'undefined\') { throw new Error(\'Require.js has not been loaded\'); };'."\n".
            'define(\'require.config\', function() { return '.str_replace('\\/', '/', (string)json_encode($data)).'; });'."\n";

        $output .= file_get_contents(__DIR__.'/bootstrap.require.js');

        $output = $this->http->stringResponse($output, 'text/javascript');
        $output->headers
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+1 year');

        return $output;
    }

    protected function _getTheme()
    {
        $themeId = $this->request['theme'];

        if (!$themeId) {
            $config = aura\theme\Config::getInstance();
            $themeId = $config->getThemeIdFor('front');
        }

        try {
            $theme = aura\theme\Base::factory($themeId);
        } catch (\Throwable $e) {
            throw Exceptional::{'df/aura/theme/NotFound'}([
                'message' => 'Theme not found',
                'http' => 404,
                'data' => $themeId
            ]);
        }

        return $theme;
    }

    protected function _getRequireConfig($theme)
    {
        $manager = fuse\Manager::getInstance();
        $dependencies = $manager->getInstalledDependenciesFor($theme);

        $paths = $shims = $maps = [];

        foreach ($dependencies as $key => $dependency) {
            if (!$dependency instanceof fuse\Dependency) {
                continue;
            }

            if (isset($dependency->map)) {
                $maps = array_merge($maps, $dependency->map);
            }

            $paths['{'.$dependency->id.'}'] = 'vendor/'.$dependency->installName;

            if (!empty($dependency->js)) {
                $js = $dependency->js;
                $main = array_shift($js);

                if (substr($main, -3) == '.js') {
                    $main = substr($main, 0, -3);
                }

                $paths[$dependency->id] = 'vendor/'.$dependency->installName.'/'.$main;
            }

            if (isset($dependency->shim)) {
                $shims[$dependency->id] = $dependency->shim;
            }
        }

        $data = ['paths' => $paths];

        if (!empty($shims)) {
            $data['shims'] = $shims;
        }

        if (!empty($maps)) {
            $data['map'] = $maps;
        }

        return $data;
    }
}
