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
use df\opal;
use df\mesh;

use DecodeLabs\Tagged\Html;

class FileSelector extends arch\node\form\SelectorDelegate implements core\lang\IAcceptTypeProcessor
{
    use core\lang\TAcceptTypeProcessor;
    use arch\node\TForm_MediaBucketAwareSelector;

    const DEFAULT_MODES = [
        'details' => 'createInlineDetailsUi',
        'select' => 'createOverlaySelectorUi',
        'upload' => 'createOverlayUploaderUi',
        'version' => 'createOverlayVersionUi'
    ];


    protected $_ownerId;
    protected $_autoSelect = false;

    // Owner
    public function setOwnerId($id)
    {
        $this->_ownerId = $id;
        return $this;
    }

    public function getOwnerId()
    {
        if ($this->_ownerId !== null) {
            return $this->_ownerId;
        } else {
            return $this->user->client->getId();
        }
    }


    // Form
    protected function init()
    {
        $this->_setupBucket();
    }

    protected function loadDelegates()
    {
        if (!$this->_bucket) {
            return;
        }

        $mode = $this->getMode();
        $accept = array_merge($this->_bucketHandler->getAcceptTypes(), $this->_acceptTypes);

        $this->loadDelegate('upload', 'CustomTempUploader')
            ->isForMany($this->_isForMany)
            ->isRequired($mode == 'upload')
            ->setAcceptTypes(...$accept);

        $this->loadDelegate('versionUpload', 'CustomTempUploader')
            ->isForOne(true)
            ->isRequired($mode == 'version')
            ->setAcceptTypes(...$accept);
    }

    // Record
    protected function _getBaseQuery($fields=null)
    {
        if ($this->_bucketHandler) {
            $accept = array_merge($this->_bucketHandler->getAcceptTypes(), $this->_acceptTypes);
        } else {
            $accept = $this->_acceptTypes;
        }

        return $this->data->media->file->select($fields)
            ->countRelation('versions')
            ->leftJoinRelation('activeVersion', 'number as version', 'fileSize', 'contentType')
            ->importRelationBlock('bucket', 'link')
            ->importRelationBlock('owner', 'link')
            ->chainIf($this->_bucket, function ($query) {
                if ($this->_bucket['slug'] == 'shared') {
                    //$query->where('bucket', '=', $this->_bucket);
                } else {
                    $query->beginWhereClause()
                        ->where('bucket', '=', $this->_bucket)
                        ->orWhereCorrelation('bucket', 'in', 'id')
                            ->from('axis://media/Bucket', 'bucket')
                            ->where('slug', '=', 'shared')
                            ->endCorrelation()
                        ->endClause();
                }
            })
            ->chainIf(!empty($accept), function ($query) use ($accept) {
                $clause = $query->beginWhereClause();

                foreach ($accept as $type) {
                    if (substr($type, -1) == '*') {
                        $type = substr($type, 0, -1);
                    }

                    if (substr($type, -1) == '/') {
                        $clause->orWhere('contentType', 'begins', $type);
                    } else {
                        $clause->orWhere('contentType', '=', $type);
                    }
                }

                $clause->endClause();
            })
            ->orderBy('fileName ASC');
    }

    protected function _applyQuerySearch(opal\query\IQuery $query, $search)
    {
        if (!$query instanceof opal\query\ISearchableQuery) {
            return;
        }

        $query->searchFor($search, [
            'fileName' => 2
        ]);
    }

    protected function _getResultDisplayName($row)
    {
        return $row['fileName'];
    }


    // Ui
    protected function _renderDetailsButtonGroup(aura\html\widget\ButtonArea $ba, $selected, $isList=false)
    {
        $mainLabel = $isList ? $this->_('Upload / search') : $this->_('Upload / select');

        if (empty($selected)) {
            $ba->push(
                $this->html->eventButton(
                        $this->eventName('beginSelect'),
                        $mainLabel
                    )
                    ->setIcon('upload')
                    ->setDisposition('positive')
                    ->shouldValidate(false)
            );
        } else {
            if (!$this->_isForMany) {
                $ba->push(
                    $this->html->eventButton(
                            $this->eventName('beginVersion'),
                            $this->_('New version')
                        )
                        ->setIcon('upload')
                        ->setDisposition('operative')
                        ->shouldValidate(false)
                );
            } else {
                $ba->push(
                    $this->html->eventButton(
                            $this->eventName('beginSelect'),
                            $mainLabel
                        )
                        ->setIcon($this->_bucket ? 'upload' : 'select')
                        ->setDisposition('operative')
                        ->shouldValidate(false)
                );
            }
        }

        if ($isList) {
            $ba->push(
                $this->html->eventButton(
                        $this->eventName('endSelect'),
                        $this->_('Update')
                    )
                    ->setIcon('refresh')
                    ->setDisposition('informative')
                    ->shouldValidate(false)
            );
        }


        if (!empty($selected)) {
            $ba->push(
                $this->html->eventButton(
                        $this->eventName('clear'),
                        $this->_('Clear')
                    )
                    ->setIcon('remove')
                    ->shouldValidate(false)
            );
        }
    }

    protected function createOverlaySelectorUiContent(aura\html\widget\Overlay $ol, $selected)
    {
        if ($this->_bucket) {
            if ($form = $this->content->findFirstWidgetOfType('Form')) {
                $form->setEncoding($form::ENC_MULTIPART);
            }

            $fs = $ol->addFieldSet($this->_('Upload new file'));
            $accept = array_merge($this->_bucketHandler->getAcceptTypes(), $this->_acceptTypes);

            $fs->addField()->push(
                $this->html->fileUpload($this['upload']->fieldName('file'))
                    ->allowMultiple($this->_isForMany)
                    ->setAcceptTypes(...$accept),

                $this->html->eventButton(
                        $this->eventName('beginUpload'),
                        $this->_('Upload')
                    )
                    ->setIcon('upload')
                    ->setDisposition('positive')
                    ->shouldValidate(false),

                $this->html->cancelEventButton(
                        $this->eventName('cancelSelect')
                    )
            );
        }

        parent::createOverlaySelectorUiContent($ol, $selected);
    }

    protected function _renderCollectionList($result)
    {
        // TODO: swap for shared !!!!!
        return $this->apex->component('~/media/files/FileList', [
                'actions' => false
            ])
            ->setCollection($result);
    }

    protected function _renderManySelected($fs, $selected)
    {
        if (empty($selected)) {
            return;
        }

        $fa = $fs->addField($this->_('Selected'));
        $fa->addClass('delegate-selector');

        foreach ($selected as $result) {
            $id = $this->_getResultId($result);
            $name = $this->_getResultDisplayName($result);

            $fa->push(
                Html::{'div.w.list.selection'}([
                    $this->html->hidden($this->fieldName('selected['.$id.']'), $id),

                    Html::{'div.body'}([
                        $this->html->icon('file', $name)
                            ->addClass('informative'),
                        ' - ',
                        Html::{'em'}($this->format->fileSize($result['fileSize']))
                    ]),

                    $this->html->buttonArea(
                        $this->_bucket ?
                            $this->html->eventButton(
                                    $this->eventName('beginVersion', $id),
                                    $this->_('New version')
                                )
                                ->shouldValidate(false)
                                ->setIcon('upload')
                                ->setDisposition('operative') : null,

                        $this->html->eventButton(
                                $this->eventName('remove', $id),
                                $this->_('Remove')
                            )
                            ->shouldValidate(false)
                            ->setIcon('remove')
                    )
                ])
            );
        }
    }

    protected function createOverlayUploaderUi(aura\html\widget\Field $fa)
    {
        if (!$this->_bucket) {
            return;
        }

        $selected = $this->createInlineDetailsUi($fa);
        $ol = $fa->addOverlay($fa->getLabelBody().' - '.$this->_('Upload new file'));

        $fs = $ol->addFieldSet($this->_('Choose your file(s)'));

        $fs->addField($this->_('Select a file'))->push(
            $this['upload']
        );

        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('upload'),
                    $this->_('Upload')
                )
                ->setIcon('upload')
                ->setDisposition('positive')
                ->shouldValidate(false),

            $this->html->cancelEventButton($this->eventName('cancelUpload'))
        );
    }

    protected function createOverlayVersionUi(aura\html\widget\Field $fa)
    {
        if (!$this->_bucket) {
            return;
        }

        $selected = $this->createInlineDetailsUi($fa);

        $fileId = $this->getStore('versionFileId');
        $file = $this->data->media->file->select('id', 'fileName')
            ->leftJoinRelation('activeVersion', 'fileSize', 'creationDate')
            ->where('id', '=', $fileId)
            ->toRow();

        if (!$file) {
            $this->switchMode('version', 'select');
            return $this->createOverlaySelectorUi($fa);
        }

        $ol = $fa->addOverlay($fa->getLabelBody().' - '.$this->_('Add new version'));

        $fs = $ol->addFieldSet($this->_('Choose your file'));
        $fs->addField($this->_('Select a file'))->push(
            $this['versionUpload']
        );

        $fs->unshift(
            $this->html->field()->push(
                $this->html->flashMessage($this->_(
                    'This will replace your current file with the one you upload in all places this file is used'
                ))->setDescription($this->_(
                    'To just replace the file in this one instance, you should clear your selection and upload a new file'
                ))->setType('warning')
            )
        );

        $fs->addField($this->_('Current file'))->push(
            $this->html->icon('file', $file['fileName'])
                ->addClass('informative'),
            ' - ',
            Html::{'em'}($this->format->fileSize($file['fileSize']))
        );

        $fs->addField($this->_('Notes'))->push(
            $this->html->textarea($this->fieldName('notes'), $this->values->notes)
        );

        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('uploadVersion'),
                    $this->_('Upload version')
                )
                ->setIcon('upload')
                ->setDisposition('positive')
                ->shouldValidate(false),

            $this->html->buttonGroup(
                $this->html->eventButton(
                        $this->eventName('clearAndUpload'),
                        $this->_('Clear selection and upload')
                    )
                    ->setIcon('remove')
                    ->setDisposition('negative')
                    ->shouldValidate(false),

                $this->html->cancelEventButton($this->eventName('cancelVersion'))
            )
        );
    }


    // Events
    protected function onEndSelectEvent()
    {
        $this->onUploadEvent();

        if ($this->values->isValid()) {
            return parent::onEndSelectEvent();
        }
    }


    protected function onBeginUploadEvent()
    {
        if (!$this->_bucket) {
            return;
        }

        $this->switchMode('select', 'upload');
        $this->onUploadEvent();
    }

    protected function onUploadEvent()
    {
        if (!$this->_bucket) {
            return;
        }

        $uploadDelegate = $this['upload'];
        $result = $uploadDelegate->apply();

        if (empty($result)) {
            return;
        }

        $result = (array)$result;

        foreach ($result as $filePath) {
            $file = $this->data->media->publishFile($filePath, $this->_bucket, [
                'owner' => $this->getOwnerId()
            ]);

            $this->addSelected((string)$file['id']);
        }

        $this->setMode('details');
        $uploadDelegate->setComplete();
    }

    protected function onCancelUploadEvent()
    {
        $this->switchMode('upload', 'select');
    }



    protected function onBeginVersionEvent($id=null)
    {
        if (!$this->_bucket) {
            return;
        }

        if (!$this->_isForMany) {
            $id = $this->getSelected();
        } else {
            if (!$this->isSelected($id)) {
                return;
            }
        }

        $this->switchMode(['details', 'select'], 'version');
        $this->setStore('versionFileId', $id);

        if ($this->_isForMany) {
            $this->onUploadVersionEvent();
        }
    }

    protected function onUploadVersionEvent()
    {
        if (!$this->_bucket
        || $this->getMode() != 'version'
        || !$this->hasStore('versionFileId')) {
            return;
        }

        $id = $this->getStore('versionFileId');

        if (!$this->isSelected($id)) {
            return;
        }

        $uploadDelegate = $this['versionUpload'];
        $result = $uploadDelegate->apply();

        if (empty($result)) {
            return;
        }

        if (!$file = $this->data->media->file->fetchByPrimary($id)) {
            return;
        }

        $version = $this->data->media->publishVersion($file, $result, [
            'owner' => $this->getOwnerId()
        ]);

        $this->setMode('details');
        $uploadDelegate->setComplete();
        $this->removeStore('versionFileId');
    }

    protected function onClearAndUploadEvent()
    {
        if (!$this->_bucket) {
            return;
        }

        if (!$this->_isForMany) {
            $this->setSelected(null);
        } else {
            if ($fileId = $this->getStore('versionFileId')) {
                $this->removeSelected($fileId);
            }
        }

        $uploadDelegate = $this['versionUpload'];
        $result = $uploadDelegate->apply();

        if (!empty($result)) {
            $result = (array)$result;

            foreach ($result as $filePath) {
                $file = $this->data->media->publishFile($filePath, $this->_bucket, [
                    'owner' => $this->getOwnerId()
                ]);

                $this->addSelected((string)$file['id']);
            }

            $this->setMode('details');
            $uploadDelegate->setComplete();
        } else {
            $this->setMode('upload');
            $this->removeStore('versionFileId');
        }
    }

    protected function onCancelVersionEvent()
    {
        $this->switchMode('version', 'details');
        $this->removeStore('versionFileId');
    }
}
