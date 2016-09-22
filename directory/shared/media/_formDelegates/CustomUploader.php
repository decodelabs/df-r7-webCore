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

class CustomUploader extends arch\node\form\Delegate implements
    arch\node\IDependentDelegate,
    arch\node\ISelectorDelegate,
    core\io\IAcceptTypeProcessor {

    use arch\node\TForm_SelectorDelegate;
    use arch\node\TForm_ValueListSelectorDelegate;
    use arch\node\TForm_DependentDelegate;
    use arch\node\TForm_MediaBucketAwareSelector;
    use core\io\TAcceptTypeProcessor;

    protected $_limit = null;
    protected $_ownerId;
    protected $_showUploadButton = false;

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

    public function shouldShowUploadButton(bool $flag=null) {
        if($flag !== null) {
            $this->_showUploadButton = $flag;
            return $this;
        }

        return $this->_showUploadButton;
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

        $this->loadDelegate('upload', 'CustomTempUploader')
            ->isRequired($this->_isRequired)
            ->isForMany($this->_isForMany)
            ->setAcceptTypes(...$accept)
            ->shouldShowUploadButton($this->_showUploadButton);
    }


// Render
    public function render($callback=null) {
        if(!$this->_bucket) {
            $this->throwError(500, 'No bucket has been set');
        }

        return $this['upload']->render(function($delegate, $available) use($callback) {
            if($this->_isForMany || empty($available)) {
                $query = $this->data->media->file->select('id as fileId', 'creationDate as time')
                    ->joinRelation('activeVersion', 'fileName', 'fileSize as size')
                    ->where('file.id', 'in', (array)$this->getSelected())
                    ->orderBy('creationDate ASC');

                if(!$this->_isForMany) {
                    $available = $query->toRow();
                } else {
                    $files = $query->toArray();
                    $count = count($available);

                    foreach($files as $i => $file) {
                        $salt = $count + $i;
                        $ts = $file['time']->toTimestamp();
                        $key = $ts.$salt;
                        $file['time'] = $ts;
                        $available[$key] = $file;
                    }

                    ksort($available);

                    foreach($available as &$file) {
                        if(!isset($file['fileId'])) {
                            $file['fileId'] = null;
                        }
                    }
                    unset($file);
                }

            }

            if(!$callback) {
                $callback = [$this, '_render'];
            }

            return core\lang\Callback($callback, $this, $available);
        });
    }

    public function _render($delegate, $available) {
        $delegate = $this['upload'];

        yield $this->html('span', null, ['id' => $delegate->getWidgetId()]);

        if($this instanceof arch\node\IDependentDelegate) {
            $messages = $this->getDependencyMessages();

            if(!empty($messages)) {
                foreach($messages as $key => $value) {
                    yield $this->html->flashMessage($value, 'warning');
                }
                return;
            }
        }

        if($messages = $this->_getSelectionErrors()) {
            yield $this->html->fieldError($messages);
        }

        if(!$this->_isForMany) {
            if($available) {
                yield $this->html('div.w-selected', function() use($delegate, $available) {
                    if(isset($available['fileId'])) {
                        yield $this->html->hidden($this->fieldName('selected'), $available['fileId']);

                        yield [
                            $this->html('span.fileName', $available['fileName']), ' ',
                            $this->html->number($this->format->fileSize($available['size']))
                        ];

                        yield ' ';
                        yield $this->html->booleanIcon(true);
                        yield ' ';

                        yield $this->html->eventButton(
                                $this->eventName('removeFile', $available['fileId']),
                                $this->_('Remove')
                            )
                            ->setDisposition('negative')
                            ->setIcon('cross')
                            ->shouldValidate(false)
                            ->addClass('remove');
                    } else {
                        yield $this->html->hidden($delegate->fieldName('selectUpload'), $available['fileName']);

                        yield [
                            $this->html('span.fileName', $available['fileName']), ' ',
                            $this->html->number($this->format->fileSize($available['size']))
                        ];

                        yield ' ';

                        yield $this->html->eventButton(
                                $delegate->eventName('removeFile', $available['fileName']),
                                $this->_('Remove')
                            )
                            ->setDisposition('negative')
                            ->setIcon('cross')
                            ->shouldValidate(false)
                            ->addClass('remove');
                    }
                });
            }
        } else {
            yield $this->html->bulletList($available, function($file) use($delegate) {
                if(isset($file['fileId'])) {
                    yield $this->html->checkbox(
                        $this->fieldName('selected[]'),
                        $this->values->selected->contains($file['fileId']),
                        [
                            $this->html('span.fileName', $file['fileName']), ' ',
                            $this->html->number($this->format->fileSize($file['size']))
                        ],
                        $file['fileId']
                    );

                    yield ' ';
                    yield $this->html->booleanIcon(true);
                    yield ' ';

                    yield $this->html->eventButton(
                            $this->eventName('removeFile', $file['fileId']),
                            $this->_('Remove')
                        )
                        ->setDisposition('negative')
                        ->setIcon('cross')
                        ->shouldValidate(false);
                } else {
                    yield $this->html->checkbox(
                        $delegate->fieldName('selectUpload['.$file['fileName'].']'),
                        $delegate->values->selectUpload->{$file['fileName']},
                        [
                            $this->html('span.fileName', $file['fileName']), ' ',
                            $this->html->number($this->format->fileSize($file['size']))
                        ]
                    );

                    yield ' ';

                    yield $this->html->eventButton(
                            $delegate->eventName('removeFile', $file['fileName']),
                            $this->_('Remove')
                        )
                        ->setDisposition('negative')
                        ->setIcon('cross')
                        ->shouldValidate(false)
                        ->addClass('remove');
                }
            })->addClass('w-selected');
        }

        yield $this->html('div.upload', [
            $input = $this->html->fileUpload($delegate->fieldName('file'), $delegate->values->file)
                ->setAcceptTypes(...$delegate->getAcceptTypes())
                ->setId($delegate->getWidgetId().'-input'),

            $this->html->label($this->_('Choose a file...'), $input)
                ->addClass('btn hidden')
                ->addClass(!empty($available) ? 'replace': null),

            $this->_showUploadButton ?
                $this->html->eventButton(
                        $delegate->eventName('upload'),
                        $this->_('Upload')
                    )
                    ->setIcon('upload')
                    ->setDisposition('positive')
                    ->shouldValidate(false)
                    ->addClass('upload')
                : null
        ]);
    }

    public function hasAnyFile() {
        if($this->hasSelection()) {
            return true;
        }

        return $this['upload']->hasAnyFile();
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

    public function onRemoveFileEvent($id) {
        if($this->_isForMany) {
            unset($this->values->selected->{$id});
        } else {
            if($this->values['selected'] == $id) {
                unset($this->values->selected);
            }
        }
    }

    protected function _sanitizeSelection($selection) {
        return (string)flex\Guid::factory($selection);
    }
}