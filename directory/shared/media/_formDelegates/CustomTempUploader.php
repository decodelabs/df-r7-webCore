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

class CustomTempUploader extends arch\node\form\Delegate implements
    arch\node\ISelectionProviderDelegate,
    core\io\IAcceptTypeProcessor {

    use arch\node\TForm_SelectorDelegate;
    use core\io\TAcceptTypeProcessor;

    protected $_showUploadButton = false;

    private $_dirChecked = false;
    private $_hasUploaded = false;

    public function shouldShowUploadButton(bool $flag=null) {
        if($flag !== null) {
            $this->_showUploadButton = $flag;
            return $this;
        }

        return $this->_showUploadButton;
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

    protected function _getFileList() {
        static $files;

        if(!isset($files)) {
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
                while(count($files) > 1) {
                    $tempDir->deleteFile(array_pop($files)['fileName']);
                }
            }
        }

        return $files;
    }

    public function render($callback=null) {
        if(!$callback) {
            $callback = [$this, '_render'];
        }

        if($form = $this->content->findFirstWidgetOfType('Form')) {
            $form->setEncoding($form::ENC_MULTIPART);
        }

        $tempDir = $this->_getTempDir();
        $files = $this->_getFileList();

        if(!$this->_isForMany) {
            $available = array_shift($files);
        } else {
            $available = $files;
        }

        return core\lang\Callback($callback, $this, $available);
    }

    public function _render($delegate, $available) {
        yield $this->html('span', null, ['id' => $this->getWidgetId()]);

        if(!$this->_isForMany) {
            if($available) {
                yield $this->html('div.w-selected', function() use($available) {
                    yield $this->html->hidden($this->fieldName('selectUpload'), $available['fileName']);

                    yield [
                        $this->html('span.fileName', $available['fileName']), ' ',
                        $this->html->number($this->format->fileSize($available['size']))
                    ];

                    yield ' ';

                    yield $this->html->eventButton(
                            $this->eventName('removeFile', $available['fileName']),
                            $this->_('Remove')
                        )
                        ->setDisposition('negative')
                        ->setIcon('cross')
                        ->shouldValidate(false);
                });
            }
        } else {
            yield $this->html->bulletList($available, function($file) {
                yield $this->html->checkbox(
                    $this->fieldName('selectUpload['.$file['fileName'].']'),
                    $this->values->selectUpload->{$file['fileName']},
                    [
                        $this->html('span.fileName', $file['fileName']), ' ',
                        $this->html->number($this->format->fileSize($file['size']))
                    ]
                );

                yield ' ';

                yield $this->html->eventButton(
                        $this->eventName('removeFile', $file['fileName']),
                        $this->_('Remove')
                    )
                    ->setDisposition('negative')
                    ->setIcon('cross')
                    ->shouldValidate(false)
                    ->addClass('remove');
            })->addClass('w-selected');
        }

        yield $this->html('div.upload', [
            $input = $this->html->fileUpload($this->fieldName('file'), $this->values->file)
                ->setAcceptTypes(...$this->getAcceptTypes())
                ->setId($this->getWidgetId().'-input'),

            $this->html->label($this->_('Choose a file...'), $input)
                ->addClass('btn hidden')
                ->addClass(!empty($available) ? 'replace': null),

            $this->_showUploadButton ?
                $this->html->eventButton(
                        $this->eventName('upload'),
                        $this->_('Upload')
                    )
                    ->setIcon('upload')
                    ->setDisposition('positive')
                    ->shouldValidate(false)
                    ->addClass('upload')
                : null
        ]);
    }

    public function getWidgetId() {
        return $this->format->slug('ctu-'.$this->getDelegateId());
    }


// Result
    protected function onUploadEvent() {
        if($this->_hasUploaded) {
            return;
        }

        unset($this->values->file);

        $uploadHandler = new link\http\upload\Handler();
        $uploadHandler->setAcceptTypes(...$this->_acceptTypes);

        if(!count($uploadHandler)) {
            return $this->http->redirect('#'.$this->getWidgetId());
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

        $this->_hasUploaded = true;
        return $this->http->redirect('#'.$this->getWidgetId());
    }

    public function hasAnyFile() {
        return (bool)count($this->_getFileList());
    }

    public function onRemoveFileEvent($fileName) {
        $tempDir = $this->_getTempDir();
        $tempDir->deleteFile($fileName);
        return $this->http->redirect('#'.$this->getWidgetId());
    }

    public function apply() {
        $this->onUploadEvent();

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
                $this->logs->logException(new \Exception('Couldn\'t find temp upload file: '.$tempDir->getPath().'/'.$fileName));

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
                    $this->logs->logException(new \Exception('Couldn\'t find temp upload file: '.$tempDir->getPath().'/'.$fileName));

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

    public function handlePostEvent(arch\node\IActiveForm $target, string $event, array $args) {
        $required = $this->_isRequired;
        $this->_isRequired = false;
        $this->handleEvent('upload');
        $this->_isRequired = $required;
    }

    protected function onComplete($success) {
        if($destination = $this->getStore('tempUploadDir')) {
            core\fs\Dir::delete($destination);
        }

        core\fs\Dir::purgeUploadTemp();
    }
}