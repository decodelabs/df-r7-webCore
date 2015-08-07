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
use df\link;

class TempUploader extends arch\form\Delegate implements 
    arch\form\ISelfContainedRenderableDelegate,
    arch\form\ISelectionProviderDelegate,
    core\io\IAcceptTypeProcessor {
    
    use arch\form\TForm_SelfContainedRenderableDelegate;
    use arch\form\TForm_SelectorDelegate;
    use core\io\TAcceptTypeProcessor;

    protected $_showUploadButton = true;
    protected $_fieldLabel = null;
    protected $_showFieldLabel = true;
    protected $_maxTempFiles = 10;

    private $_dirChecked = false;

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

    protected function _getTempDir() {
        $destination = $this->getStore('tempUploadDir');

        if(!$this->_dirChecked) {
            $dir = core\fs\Dir::createUploadTemp($destination);
            $this->_dirChecked = true;
            $this->setStore('tempUploadDir', $dir->getPath());

            return $dir;
        }

        return core\fs\Dir::factory($destination);
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $fs) {
        if($form = $this->content->findFirstWidgetOfType('Form')) {
            $form->setEncoding($form::ENC_MULTIPART);
        }

        $tempDir = $this->_getTempDir();
        $files = [];
        $i = 0;

        foreach($tempDir->scanFiles() as $fileName => $file) {
            $time = $file->getLastModified();

            $files[$time.$i] = [
                'fileName' => $fileName,
                'size' => $file->getSize(),
                'time' => $time
            ];

            $i++;
        }

        krsort($files);

        if(!$this->_isForMany) {
            while(count($files) > $this->_maxTempFiles) {
                $file = array_pop($files);
                $tempDir->deleteFile($file['fileName']);
            }
        }

        if($this->_showFieldLabel) {
            $label = $this->_fieldLabel !== null ? $this->_fieldLabel : $this->_('File');
        } else {
            $label = null;
        }

        $fa = $fs->addFieldArea($label)
            ->isStacked($this->_isStacked)
            ->push(
                $this->html->fileUpload($this->fieldName('file'), $this->values->file)
                    ->setAcceptTypes($this->getAcceptTypes())
            );

        if($this->_showFieldLabel) {
            $fa->isRequired($this->_isRequired);
        }

        if($this->_showUploadButton) {
            $fa->push(
                $this->html->eventButton(
                        $this->eventName('upload'),
                        $this->_('Upload')
                    )
                    ->setIcon('upload')
                    ->setDisposition('positive')
                    ->shouldValidate(false)
            );
        }

        $fileCount = count($files);

        if($fileCount) {
            $hasMany = !$this->_isForMany && $fileCount > 1;

            $list = $this->html->collectionList($files)
                ->shouldShowHeader(false)
                ->addField('fileName', function($file) use($hasMany) {
                    if(!$this->_isForMany) {
                        if($hasMany) {
                            return $this->html->radioButton(
                                $this->fieldName('selectUpload'),
                                $this->values->selectUpload,
                                $file['fileName'],
                                $file['fileName']
                            );
                        } else {
                            return [
                                $file['fileName'],
                                $this->html->hidden($this->fieldName('selectUpload'), $file['fileName'])
                            ];
                        }
                    } else {
                        return $this->html->checkbox(
                            $this->fieldName('selectUpload['.$file['fileName'].']'),
                            $this->values->selectUpload->{$file['fileName']},
                            $file['fileName']
                        );
                    }
                })
                ->addField('size', function($file) {
                    return $this->format->fileSize($file['size']);
                })
                ->addField('time', $this->_('Uploaded'), function($file) {
                    return $this->format->timeFromNow($file['time']);
                });


            $fs->addFieldArea()->isStacked($this->_isStacked)->push($list);
        }
    }


// Result
    protected function _onUploadEvent() {
        unset($this->values->file);

        $uploadHandler = new link\http\upload\Handler();
        $uploadHandler->setAcceptTypes($this->_acceptTypes);

        if(!count($uploadHandler)) {
            return;
        }

        if(!$this->_isForMany) {
            unset($this->values->selectUpload);
        }

        $tempDir = $this->_getTempDir();

        if($file = $uploadHandler[$this->fieldName('file')]) {
            $file->upload($tempDir, $this->values->file);

            if($file->isSuccess()) {
                if(!$this->_isForMany) {
                    $this->values->selectUpload = $file->getBasename();
                } else {
                    $this->values->selectUpload[$file->getBasename()] = true;
                }
            }
        }
    }

    public function getUploadedFileNames() {
        if(!$this->_isForMany) {
            $fileName = $this->values['selectUpload'];

            if(!strlen($fileName)) {
                return null;
            }

            return $fileName;
        } else {
            $fileNames = $this->values->selectUpload->getKeys();

            if(empty($fileNames)) {
                return [];
            }

            $tempDir = $this->_getTempDir();
            $output = [];

            foreach($fileNames as $fileName) {
                if(!$tempDir->hasFile($fileName)) {
                    return [];
                }

                $output[] = $fileName;
            }

            return $output;
        }
    }

    public function apply() {
        $this->_onUploadEvent();

        if(!$this->values->file->isValid()) {
            return null;
        }

        if(!$this->_isForMany) {
            $fileName = $this->values['selectUpload'];

            if(!strlen($fileName)) {
                if($this->_isRequired) {
                    $this->values->file->addError('required', $this->_(
                        'You must upload a file'
                    ));
                }

                return null;
            }

            $tempDir = $this->_getTempDir();

            if(!$tempDir->hasFile($fileName)) {
                $this->values->file->addError('notFound', $this->_(
                    'Something went wrong while transferring your file - please try again'
                ));

                return null;
            }

            return $tempDir.'/'.$fileName;
        } else {
            $fileNames = $this->values->selectUpload->getKeys();

            if(empty($fileNames)) {
                if($this->_isRequired) {
                    $this->values->file->addError('required', $this->_(
                        'You must upload at least one file'
                    ));
                }

                return [];
            }

            $tempDir = $this->_getTempDir();
            $output = [];

            foreach($fileNames as $fileName) {
                if(!$tempDir->hasFile($fileName)) {
                    $this->values->file->addError('notFound', $this->_(
                        'Something went wrong while transferring your file - please try again'
                    ));

                    unset($this->values->selectUpload->{$fileName});
                    return [];
                }

                $output[] = $tempDir.'/'.$fileName;
            }

            return $output;
        }
    }

    public function handleDelegateEvent($delegateId, $event, $args) {
        $this->_onUploadEvent();
    }

    protected function _onComplete($success) {
        if($destination = $this->getStore('tempUploadDir')) {
            core\fs\Dir::delete($destination);
        }

        core\fs\Dir::purgeUploadTemp();
    }
}