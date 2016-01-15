<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\media\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\mesh;
use df\flex;

class SimpleUploader extends arch\node\form\Delegate implements
    arch\node\ISelfContainedRenderableDelegate,
    arch\node\IDependentDelegate,
    arch\node\ISelectorDelegate,
    core\io\IAcceptTypeProcessor {

    use arch\node\TForm_SelfContainedRenderableDelegate;
    use arch\node\TForm_SelectorDelegate;
    use arch\node\TForm_ValueListSelectorDelegate;
    use arch\node\TForm_DependentDelegate;
    use arch\node\TForm_MediaBucketAwareSelector;
    use core\io\TAcceptTypeProcessor;

    protected $_showUploadButton = true;
    protected $_fieldLabel = null;
    protected $_showFieldLabel = true;
    protected $_limit = null;
    protected $_ownerId;

    public function setOwnerId($id) {
        $this->_ownerId = $id;
        return $this;
    }

    public function getOwnerId() {
        if($this->_ownerId !== null) {
            return $this->_ownerId;
        } else {
            return $this->user->client->getId();
        }
    }

    public function shouldShowUploadButton($flag=null) {
        if($flag !== null) {
            $this->_showUploadButton = (bool)$flag;
            return $this;
        }

        return $this->_showUploadButton;
    }

    public function setFieldLabel($label) {
        $this->_fieldLabel = $label;
        return $this;
    }

    public function getFieldLabel() {
        return $this->_fieldLabel;
    }

    public function shouldShowFieldLabel($flag=null) {
        if($flag !== null) {
            $this->_showFieldLabel = (bool)$flag;
            return $this;
        }

        return $this->_showFieldLabel;
    }

    public function setFileLimit($limit) {
        if($limit) {
            $limit = (int)$limit;
        } else {
            $limit = null;
        }

        $this->_limit = $limit;
        return $this;
    }

    public function getFileLimit() {
        return $this->_limit;
    }

    public function getSourceEntityLocator() {
        return new mesh\entity\Locator('upload://File');
    }

    protected function init() {
        $this->_setupBucket();
    }

    protected function loadDelegates() {
        if($this->_bucketHandler) {
            $accept = array_merge($this->_bucketHandler->getAcceptTypes(), $this->_acceptTypes);
        } else {
            $accept = $this->_acceptTypes;
        }

        $this->loadDelegate('upload', 'TempUploader')
            ->isRequired($this->_isRequired)
            ->isForMany($this->_isForMany)
            ->setAcceptTypes($accept);
    }


// Render
    public function renderContainerContent(aura\html\widget\IContainerWidget $fs) {
        if(!$this->_bucket) {
            $fs->addFlashMessage($this->_('No bucket has been selected'), 'warning');
            return;
        }

        $delegate = $this['upload']
            ->shouldShowUploadButton($this->_showUploadButton)
            ->setFieldLabel($this->_fieldLabel)
            ->shouldShowFieldLabel($this->_showFieldLabel);

        $delegate->renderContainerContent($fs);
    }

    public function apply() {
        if(!$this->_bucket) {
            return;
        }

        $delegate = $this['upload'];
        $delegate->isRequired($this->_isRequired && !$this->hasSelection());
        $delegate->values->file->clearErrors();

        if(!$this->_isForMany) {
            if($filePath = $delegate->apply()) {
                $file = $this->data->media->publishFile($filePath, $this->_bucket, [
                    'owner' => $this->getOwnerId()
                ]);
                $this->setSelected($file['id']);
            }
        } else {
            $filePaths = $delegate->apply();
            $ids = $this->getSelected();

            if(!empty($filePaths)) {
                foreach($filePaths as $filePath) {
                    $file = $this->data->media->publishFile($filePath, $this->_bucket, [
                        'owner' => $this->getOwnerId()
                    ]);

                    $ids[] = $file['id'];
                }
            }

            if($this->_limit) {
                $ids = array_slice($ids, count($ids) - $this->_limit);
            }

            $this->setSelected($ids);
        }


        if($this->_isRequired && !$this->hasSelection()) {
            if($this->_isForMany) {
                $delegate->values->file->addError('required', $this->_(
                    'You must upload at least one file'
                ));
            } else {
                $delegate->values->file->addError('required', $this->_(
                    'You must upload a file'
                ));
            }
        }


        if($this->isValid()) {
            $delegate->setComplete(true);
        }

        return $this->getSelected();
    }

    protected function _sanitizeSelection($selection) {
        return (string)flex\Guid::factory($selection);
    }
}