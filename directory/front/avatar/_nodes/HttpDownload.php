<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\avatar\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;

use df\arch;

class HttpDownload extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $id = $this->request['user'];
        $size = $this->request->query->get('size', 400);

        if ($id == 'default') {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        try {
            $version = $this->data->media->fetchSingleUserVersionForDownload($id, 'Avatar');

            return $this->media->serveImage(
                $version['fileId'],
                $version['id'],
                $version['isActive'],
                $version['contentType'],
                $version['fileName'],
                '[cz:' . $size . '|' . $size . ']',
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
