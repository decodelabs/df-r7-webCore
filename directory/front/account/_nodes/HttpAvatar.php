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

use DecodeLabs\Glitch;

class HttpAvatar extends arch\node\Form
{
    const DEFAULT_EVENT = 'upload';
    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    protected $_file;

    protected function init()
    {
        $this->_file = $this->data->media->fetchSingleUserFile($this->user->client->getId(), 'Avatar');
    }

    protected function getInstanceId()
    {
        return null;
    }

    public function getFile()
    {
        return $this->_file;
    }

    protected function loadDelegates()
    {
        $this->loadDelegate('upload', 'media/CustomTempUploader')
            ->isForOne(true)
            //->shouldShowUploadButton(false)
            ;
    }

    protected function createUi()
    {
        $this->view
            ->setCanonical('account/avatar')
            ->canIndex(false);

        if ($this->_file) {
            $versions = $this->_file->versions->fetch()
                ->where('purgeDate', '=', null)
                ->orderBy('creationDate DESC')
                ->toArray();
        } else {
            $versions = null;
        }

        $form = $this->content->addForm();
        $form->setEncoding($form::ENC_MULTIPART);
        $fs = $form->addFieldSet($this->_('Update your avatar'));

        if (!empty($versions)) {
            $fa = $fs->addField($this->_('Your images'));
            $activeId = $this->_file['#activeVersion'];

            foreach ($versions as $version) {
                $fa->push(
                    $this->html('div.w.card.avatar', [
                        $this->html->tag('input', [
                            'type' => 'image',
                            'src' => $this->uri($version->getImageUrl('[cz:150|150]')),
                            'name' => 'formEvent',
                            'value' => $this->eventName('selectVersion', $version['id'])
                        ]),

                        $this->html->eventButton($this->eventName('deleteVersion', $version['id']), $this->_('Delete'))
                            ->setIcon('delete')
                    ])
                    ->setStyle('display', 'inline-block')
                    ->chainIf($activeId == $version['id'], function ($widget) {
                        $widget->addClass('active');
                    })
                );
            }
        }

        $fs->addField($this->_('Image'))->push(
            $this['upload']
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->eventButton('upload', $this->_('Upload'))
                ->setIcon('upload')
                ->setDisposition('positive'),

            $this->html->cancelEventButton()
        );
    }

    protected function onSelectVersionEvent($version)
    {
        if (!$this->_file) {
            return;
        }

        return $this->complete(function () use ($version) {
            $version = $this->data->fetchForAction(
                'axis://media/Version',
                $version
            );

            $this->data->media->activateVersion($version['file'], $version);
            $this->data->user->cache->setAvatarCacheTime();
        });
    }

    protected function onDeleteVersionEvent($version)
    {
        if (!$this->_file) {
            return;
        }

        $version = $this->data->fetchForAction(
            'axis://media/Version',
            $version
        );

        if ((string)$version['#file'] != (string)$this->_file['id']) {
            throw Glitch::EForbidden([
                'message' => 'Not your file',
                'http' => 403
            ]);
        }

        $this->data->media->purgeVersion($version);
        $this->data->user->cache->setAvatarCacheTime();

        $active = $this->_file->versions->select()
            ->where('purgeDate', '=', null)
            ->count();

        if (!$active) {
            $this->data->media->deleteFile($this->_file);
        }
    }

    protected function onUploadEvent()
    {
        $filePath = $this['upload']->apply();

        return $this->complete(function () use ($filePath) {
            if ($filePath) {
                $this->data->media->publishFile($filePath, 'Avatar');
            }

            $this->data->user->cache->setAvatarCacheTime();
            $this->comms->flash('avatar.save', $this->_('Your avatar has been successfully updated'), 'success');
        });
    }
}
