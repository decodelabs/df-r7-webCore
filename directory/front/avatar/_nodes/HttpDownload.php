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
                throw core\Error::{'core/fs/ENotFound'}([
                    'message' => 'File not found',
                    'http' => 404
                ]);
            }

            $type = core\fs\Type::fileToMime($absolutePath);

            if(substr($type, 0, 6) != 'image/') {
                throw core\Error::{'core/fs/EType,EForbidden'}([
                    'message' => 'File not image',
                    'http' => 403
                ]);
            }

            $descriptor = new neon\raster\Descriptor($absolutePath, $type);

            if(isset($this->request['size'])) {
                $descriptor->applyTransformation('[rs:'.$size.'|'.$size.']');
            }

            return $this->http->fileResponse($descriptor->getLocation())
                ->setFileName($descriptor->getFileName())
                ->setContentType($descriptor->getContentType());
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
            } catch(\Throwable $e) {
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
