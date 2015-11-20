<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAvatar extends arch\node\Form {

    const DEFAULT_EVENT = 'upload';
    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    protected $_file;

    protected function init() {
        $this->_file = $this->data->media->fetchSingleUserFile($this->user->client->getId(), 'Avatar');
    }

    protected function loadDelegates() {
        $this->loadDelegate('upload', 'media/TempUploader')
            ->isForOne(true)
            ->shouldShowUploadButton(false)
            ;
    }

    protected function createUi() {
        if($this->_file) {
            $versions = $this->_file->versions->fetch()
                ->where('purgeDate', '=', null)
                ->orderBy('creationDate DESC')
                ->toArray();
        } else {
            $versions = null;
        }

        $this->content->push(
            $this->apex->component(
                '~front/account/Avatar',
                $this, $this->_file, $versions
            )
        );
    }

    protected function onSelectVersionEvent($version) {
        if(!$this->_file) {
            return;
        }

        return $this->complete(function() use($version) {
            $version = $this->data->fetchForAction(
                'axis://media/Version',
                $version
            );

            $this->data->media->activateVersion($version['file'], $version);
            $this->data->user->cache->setAvatarCacheTime();
        });
    }

    protected function onDeleteVersionEvent($version) {
        if(!$this->_file) {
            return;
        }

        $version = $this->data->fetchForAction(
            'axis://media/Version',
            $version
        );

        if((string)$version['#file'] != (string)$this->_file['id']) {
            $this->throwError(403, 'Not your file');
        }

        $this->data->media->purgeVersion($version);
        $this->data->user->cache->setAvatarCacheTime();
    }

    protected function onUploadEvent() {
        $filePath = $this['upload']->apply();

        return $this->complete(function() use($filePath) {
            if($filePath) {
                $this->data->media->publishFile($filePath, 'Avatar');
                $this->data->user->cache->setAvatarCacheTime();
            }

            $this->comms->flashSaveSuccess('photo');
        });
    }

}