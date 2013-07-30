<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\theme;

use df;
use df\core;
use df\arch;
use df\aura;
use df\neon;
use df\flow;

class HttpController extends arch\Controller {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL; 
    
    public function assetsAction() {
        $theme = aura\theme\Base::factory($this->request->query['theme']);
        
        if(!$absolutePath = $theme->findAsset($this->application, $this->request->query['file'])) {
            $this->throwError(404, 'File not found');
        }

        $type = null;

        if(isset($this->request->query->transform)) {
            $type = flow\mime\Type::fileToMime($absolutePath);

            if(substr($type, 0, 6) == 'image/') {
                $cache = neon\raster\Cache::getInstance($this->application);
                $absolutePath = $cache->getTransformationFilePath($absolutePath, $this->request->query['transform']);
            }
        }
        
        $output = $this->http->fileResponse($absolutePath);

        if($type) {
            $output->setContentType($type);
        }
        
        return $output;
    }
}