<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\assets\_actions;

use df;
use df\core;
use df\arch;
use df\aura;
use df\neon;

class HttpDownload extends arch\Action {
    
    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL; 
    
    public function execute() {
        if(!$absolutePath = df\Launchpad::$loader->findFile('apex/assets/'.$this->request->query['file'])) {
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

        if(isset($this->request->query->attachment)) {
            $output->setAttachmentFileName(basename($absolutePath));
        }
        
        return $output;
    }
}