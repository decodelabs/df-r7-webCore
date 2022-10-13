<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\avatar\_nodes;

use df\arch;
use df\aura;
use df\neon;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;
use DecodeLabs\Typify;

class HttpDownload extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $id = $this->request['user'];
        $size = $this->request->query->get('size', 400);

        if ($id == 'default') {
            $theme = aura\theme\Base::factory($this->context);

            if (!$absolutePath = $theme->findAsset($this->data->user->avatarConfig->getDefaultAvatarPath())) {
                throw Exceptional::{'df/core/fs/NotFound'}([
                    'message' => 'File not found',
                    'http' => 404
                ]);
            }

            $type = Typify::detect($absolutePath);

            if (substr($type, 0, 6) != 'image/') {
                throw Exceptional::{'df/core/fs/Type,Forbidden'}([
                    'message' => 'File not image',
                    'http' => 403
                ]);
            }

            $descriptor = new neon\raster\Descriptor($absolutePath, $type);

            if (isset($this->request['size'])) {
                $descriptor->applyTransformation('[rs:'.$size.'|'.$size.']');
            }

            return Legacy::$http->fileResponse($descriptor->getLocation())
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
            } catch (\Throwable $e) {
                $url = $this->avatar->getGravatarUrl(
                    $this->data->user->client->select('email')
                        ->where('id', '=', $id)
                        ->toValue('email'),
                    $size
                );

                $output = Legacy::$http->redirect($url)->isAlternativeContent(true);
                $output->headers->setCacheExpiration('15 minutes');
                return $output;
            }
        }
    }
}
