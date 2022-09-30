<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Mode;
use DecodeLabs\Atlas\File;
use DecodeLabs\Dictum;
use DecodeLabs\Exemplar\Writer as XmlWriter;
use DecodeLabs\Genesis;

class HttpSitemap extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsHtml()
    {
        return $this->http->redirect('/sitemap.xml');
    }

    public function executeAsXml()
    {
        if (Genesis::$environment->isDevelopment()) {
            $xml = $this->_generateXml();
            return $this->http->stringResponse((string)$xml, 'application/xml');
        }

        $path = Genesis::$hub->getLocalDataPath().'/sitemap/'.Genesis::$environment->getMode().'.xml';
        $rebuild = false;
        $file = Atlas::file($path);

        if (isset($this->request['rebuild'])
        || !$file->exists()
        || (time() - $file->getLastModified() > (60 * 60 * 6))
        || !$file->getSize()) {
            $rebuild = true;
            $file->open(Mode::READ_WRITE_TRUNCATE);
        }

        if ($rebuild) {
            $this->_generateXml($file);
            $file->close();
        }

        return $this->http->fileResponse($file);
    }

    protected function _generateXml(File $file=null)
    {
        if ($file) {
            $xml = XmlWriter::createFile($file->getPath());
        } else {
            $xml = XmlWriter::create();
        }

        $xml->writeHeader();
        $xml->startElement('urlset');
        $xml->setAttributes([
            'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
        ]);

        foreach ($this->_scanNodes() as $class => $entry) {
            $this->_writeEntry($xml, $entry);
        }

        if ($transformer = arch\Transformer::factory($this->context)) {
            foreach ($transformer->getSitemapEntries() as $entry) {
                $this->_writeEntry($xml, $entry);
            }
        }

        $xml->endElement();
        $xml->finalize();
        return $xml;
    }

    protected function _writeEntry($xml, $entry)
    {
        $xml->startElement('url');
        $xml->writeElement('loc', $entry->getUrl());

        if ($date = $entry->getLastModifiedDate()) {
            $xml->writeElement('lastmod', $date->format('c'));
        }

        if ($change = $entry->getChangeFrequency()) {
            $xml->writeElement('changefreq', $change);
        }

        if (null !== ($priority = $entry->getPriority())) {
            $xml->writeElement('priority', $priority);
        }

        $xml->endElement('url');
    }



    protected function _scanNodes()
    {
        $fileList = df\Launchpad::$loader->lookupFileListRecursive('apex/directory', ['php'], function ($path) {
            return basename($path) == '_nodes';
        });

        foreach ($fileList as $key => $path) {
            $basename = substr(basename($path), 0, -4);

            if (substr($basename, 0, 4) != 'Http') {
                continue;
            }

            $keyParts = explode('/', dirname($key));

            if ($keyParts[0] == 'shared') {
                continue;
            }

            $class = 'df\\apex\\directory\\'.implode('\\', $keyParts).'\\'.$basename;

            if (!class_exists($class)) {
                continue;
            }

            array_pop($keyParts);

            if ($keyParts[0] == 'front') {
                array_shift($keyParts);
            } else {
                $keyParts[0] = '~'.$keyParts[0];
            }

            $request = arch\Request::factory(implode('/', $keyParts).'/'.Dictum::actionSlug(substr($basename, 4)));
            $context = $this->context->spawnInstance($request);
            $node = new $class($context);
            $entries = $node->getSitemapEntries();

            if (!core\collection\Util::isIterable($entries)) {
                continue;
            }

            foreach ($entries as $date => $entry) {
                if (is_string($date)) {
                    $date = core\time\Date::factory($date);
                } elseif (!$date instanceof core\time\Date) {
                    $date = null;
                }

                if (!$entry instanceof arch\navigation\ISitemapEntry) {
                    $entry = new arch\navigation\SitemapEntry($this->uri((string)$entry), $date);
                }

                yield $class => $entry;
            }
        }
    }
}
