<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\dfKit\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\R7\Legacy;

use df\arch;
use df\aura;
use df\fuse;

class HttpBootstrapSystem extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs()
    {
        $output = Legacy::$http->fileResponse(__DIR__ . '/bootstrap.system.js');
        $output->headers
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+1 year');

        return $output;
    }

    public function executeAsJson()
    {
        $config = $this->_getRequireConfig($this->_getTheme());

        $output = Legacy::$http->jsonResponse($config);
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
            $themeId = Legacy::getThemeIdFor('front');
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
        $cts = Genesis::$build->getCacheBuster();

        $paths = [
            'app/' => '../assets/app/',
            'admin/' => '../assets/admin/',
            'assets/' => '../assets/',
            'theme/' => '../../theme/',
            'vendor-static/' => '../assets/vendor-static/',
            'df-kit/' => '../assets/lib/df-kit/'
        ];

        $dfKit = [
            'ajax', 'core', 'flash-messages', 'markdown',
            'mediaelement', 'modal', 'pushy'
        ];

        foreach ($dfKit as $lib) {
            $paths['df-kit/' . $lib] = '../assets/lib/df-kit/' . $lib . '.js?cts=' . $cts;
        }

        foreach ($dependencies as $key => $dependency) {
            if (!$dependency instanceof fuse\Dependency) {
                continue;
            }

            $paths['{' . $dependency->id . '}/'] = '../assets/vendor/' . $dependency->installName . '/';

            if (!empty($dependency->js)) {
                $js = $dependency->js;
                $main = array_shift($js);
                $paths[$dependency->id] = '../assets/vendor/' . $dependency->installName . '/' . $main;
            }
        }

        $paths = array_merge($paths, $theme->getImportMap());

        return ['imports' => $paths];
    }
}
