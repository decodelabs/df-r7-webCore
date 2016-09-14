<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\avatar\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\neon;

class HttpDownload extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $id = $this->request['user'];
        $size = $this->request->query->get('size', 400);

        if($id == 'default') {
            $theme = aura\theme\Base::factory($this->context);

            if(!$absolutePath = $theme->findAsset($this->data->user->avatarConfig->getDefaultAvatarPath())) {
                $this->throwError(404, 'File not found');
            }

            $type = core\fs\Type::fileToMime($absolutePath);

            if(substr($type, 0, 6) != 'image/') {
                $this->throwError(404, 'File not found');
            }

            if(isset($this->request['size'])) {
                $cache = neon\raster\Cache::getInstance();
                $absolutePath = $cache->getTransformationFilePath($absolutePath, '[rs:'.$size.'|'.$size.']');
            }

            $output = $this->http->fileResponse($absolutePath);
            $output->setContentType($type);

            return $output;
        } else {
            try {
                $version = $this->data->media->fetchSingleUserVersionForDownload($id, 'Avatar');

                return $this->media->serveImage(
                    $version['fileId'],
                    $version['id'],
                    $version['isActive'],
                    $version['contentType'],
                    $version['fileName'],
                    '[cz:'.$size.'|'.$size.']',
                    $version['creationDate']
                );
            } catch(\Exception $e) {
                $url = $this->avatar->getGravatarUrl(
                    $this->data->user->client->select('email')
                        ->where('id', '=', $id)
                        ->toValue('email'),
                    $size
                );

                $output = $this->http->redirect($url)->isAlternativeContent(true);
                $output->headers->setCacheExpiration('15 minutes');
                return $output;
            }
        }
    }
}