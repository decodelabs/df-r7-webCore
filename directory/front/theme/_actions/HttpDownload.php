<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\theme\_actions;

use df;
use df\core;
use df\arch;
use df\aura;
use df\neon;

class HttpDownload extends arch\Action {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL; 
    
    public function execute() {
        $theme = aura\theme\Base::factory($this->request->query['theme']);
        
        if(!$absolutePath = $theme->findAsset($this->request->query['file'])) {
            $this->throwError(404, 'File not found');
        }

        $type = null;

        if(isset($this->request->query->transform)) {
            $type = core\io\Type::fileToMime($absolutePath);

            if(substr($type, 0, 6) == 'image/') {
                $cache = neon\raster\Cache::getInstance();
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