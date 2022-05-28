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

class HttpBootstrapSystem extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs()
    {
        $output = $this->http->fileResponse(__DIR__.'/bootstrap.system.js', 'text/javascript');
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

        $output = $this->http->jsonResponse($config);
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
        $cts = df\Launchpad::$compileTimestamp ?? time();

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
            $paths['df-kit/'.$lib] = '../assets/lib/df-kit/'.$lib.'.js?cts='.$cts;
        }

        foreach ($dependencies as $key => $dependency) {
            if (!$dependency instanceof fuse\Dependency) {
                continue;
            }

            $paths['{'.$dependency->id.'}/'] = '../assets/vendor/'.$dependency->installName.'/';

            if (!empty($dependency->js)) {
                $js = $dependency->js;
                $main = array_shift($js);
                $paths[$dependency->id] = '../assets/vendor/'.$dependency->installName.'/'.$main;
            }
        }

        $paths = array_merge($paths, $theme->getImportMap());

        return ['imports' => $paths];
    }
}
