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

use DecodeLabs\Atlas;
use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;
use DecodeLabs\Tagged as Html;

class CustomTempUploader extends arch\node\form\Delegate implements
    arch\node\ISelectionProviderDelegate,
    aura\html\IRenderable,
    aura\html\widget\IFieldDataProvider,
    core\lang\IAcceptTypeProcessor,
    core\IStringProvider
{
    use arch\node\TForm_SelectorDelegate;
    use core\constraint\TDisableable;
    use core\lang\TAcceptTypeProcessor;
    use core\TStringProvider;

    protected $_showUploadButton = false;
    protected $_chooseLabel = null;
    protected $_avScan = false;

    private $_dirChecked = false;
    private $_hasUploaded = false;
    private $_fileList;

    public function shouldShowUploadButton(bool $flag=null)
    {
        if ($flag !== null) {
            $this->_showUploadButton = $flag;
            return $this;
        }

        return $this->_showUploadButton;
    }

    public function setChooseLabel(?string $label)
    {
        $this->_chooseLabel = $label;
        return $this;
    }

    public function getChooseLabel(): ?string
    {
        return $this->_chooseLabel;
    }

    public function shouldAvScan(bool $flag=null)
    {
        if ($flag !== null) {
            $this->_avScan = $flag;
            return $this;
        }

        return $this->_avScan;
    }

    protected function _getTempDir()
    {
        $destination = $this->getStore('tempUploadDir');

        if (!$this->_dirChecked) {
            $dir = link\http\upload\Handler::createUploadTemp($destination);
            $this->_dirChecked = true;
            $this->setStore('tempUploadDir', $dir->getPath());

            return $dir;
        }

        return Atlas::dir($destination);
    }

    protected function _getFileList()
    {
        if (!isset($this->_fileList)) {
            $tempDir = $this->_getTempDir();
            $files = [];
            $i = 0;

            foreach ($tempDir->scanFiles() as $fileName => $file) {
                $time = $file->getLastModified();

                $files[$time.$i] = [
                    'fileName' => $fileName,
                    'uploadId' => basename((string)$tempDir),
                    'size' => $file->getSize(),
                    'time' => $time
                ];

                $i++;
            }

            krsort($files);

            if (!$this->_isForMany) {
                while (count($files) > 1) {
                    $tempDir->deleteFile(array_pop($files)['fileName']);
                }
            }

            $this->_fileList = $files;
        }

        return $this->_fileList;
    }


    // Field data
    public function getErrors(): array
    {
        return $this->values->file->getErrors();
    }


    // Render
    public function toString(): string
    {
        return aura\html\ElementContent::normalize($this->render());
    }

    public function render($callback=null)
    {
        if (!$callback) {
            $callback = [$this, '_render'];
        }

        if ($form = $this->content->findFirstWidgetOfType('Form')) {
            $form->setEncoding($form::ENC_MULTIPART);
        }

        $tempDir = $this->_getTempDir();
        $files = $this->_getFileList();

        if (!$this->_isForMany) {
            $available = array_shift($files);
        } else {
            $available = $files;
        }

        return core\lang\Callback($callback, $this, $available);
    }

    public function _render($delegate, $available)
    {
        yield Html::{'span'}(null, ['id' => $this->getWidgetId()]);

        if (!$this->_isForMany) {
            if ($available) {
                yield Html::{'div.w.list.selection'}(function () use ($available) {
                    yield $this->html->hidden($this->fieldName('selectUpload'), $available['fileName']);

                    yield [
                        Html::{'span.fileName'}($available['fileName']), ' ',
                        Html::$number->fileSize($available['size'])
                    ];

                    yield ' ';

                    yield $this->html->eventButton(
                            $this->eventName('removeFile', $available['fileName']),
                            $this->_('Remove')
                        )
                        ->setDisposition('negative')
                        ->setIcon('cross')
                        ->shouldValidate(false)
                        ->addClass('remove iconOnly');
                });
            }
        } else {
            yield Html::uList($available, function ($file) {
                yield $this->html->checkbox(
                    $this->fieldName('selectUpload['.$file['fileName'].']'),
                    $this->values->selectUpload->{$file['fileName']},
                    [
                        Html::{'span.fileName'}($file['fileName']), ' ',
                        Html::$number->fileSize($file['size'])
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
                    ->addClass('remove iconOnly');
            })->addClass('w selection');
        }

        yield Html::{'div.upload'}([
            $input = $this->html->fileUpload($this->fieldName('file'), $this->values->file)
                ->allowMultiple($this->_isForMany)
                ->setAcceptTypes(...$this->getAcceptTypes())
                ->setId($this->getWidgetId().'-input'),

            $this->html->label($this->_chooseLabel ?? $this->_('Choose a file...'), $input)
                ->addClass('btn hidden')
                ->addClass(!empty($available) ? 'replace' : null),

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

    public function getWidgetId()
    {
        return Dictum::slug('ctu-'.$this->getDelegateId());
    }


    // Result
    protected function onUploadEvent()
    {
        if ($this->_hasUploaded) {
            return;
        }

        $this->_hasUploaded = true;
        unset($this->values->file);

        $uploadHandler = new link\http\upload\Handler();
        $uploadHandler->setAcceptTypes(...$this->_acceptTypes);
        $uploadHandler->shouldAvScan($this->_avScan);

        if (!count($uploadHandler)) {
            return Legacy::$http->redirect('#'.$this->getWidgetId());
        }

        $tempDir = $this->_getTempDir();
        $localName = $this->fieldName('file');

        foreach ($uploadHandler as $key => $file) {
            if (0 !== strpos($key, $localName)) {
                continue;
            }

            if (!$this->_isForMany) {
                unset($this->values->selectUpload);
            }

            $file->upload($tempDir, $this->values->file);

            if ($file->isSuccess()) {
                if (!$this->_isForMany) {
                    $this->values->selectUpload = $file->getBasename();
                    break;
                } else {
                    $this->values->selectUpload[$file->getBasename()] = true;
                }
            }
        }

        return Legacy::$http->redirect('#'.$this->getWidgetId());
    }

    public function hasAnyFile()
    {
        return (bool)count($this->_getFileList());
    }

    public function onRemoveFileEvent($fileName=null)
    {
        $tempDir = $this->_getTempDir();

        if ($fileName !== null) {
            $tempDir->deleteFile($fileName);
        } else {
            if ($tempDir->countFiles() === 1) {
                $tempDir->emptyOut();
            }
        }

        return Legacy::$http->redirect('#'.$this->getWidgetId());
    }

    public function apply(): array|string|null
    {
        $this->onUploadEvent();

        if (!$this->values->file->isValid()) {
            return null;
        }

        if (!$this->_isForMany) {
            $fileName = $this->values['selectUpload'];

            if (!strlen($fileName)) {
                if ($this->_isRequired) {
                    $this->values->file->addError('required', $this->_(
                        'You must upload a file'
                    ));
                }

                return null;
            }

            $tempDir = $this->_getTempDir();

            if (!$tempDir->hasFile($fileName)) {
                $this->logs->logException(
                    Exceptional::{'df/core/fs/NotFound,TempNotFound'}(
                        'Couldn\'t find temp upload file: '.$tempDir->getPath().'/'.$fileName
                    )
                );

                $this->values->file->addError('notFound', $this->_(
                    'Something went wrong while transferring your file - please try again'
                ));

                return null;
            }

            return $tempDir.'/'.$fileName;
        } else {
            $fileNames = $this->values->selectUpload->getKeys();

            if (empty($fileNames)) {
                if ($this->_isRequired) {
                    $this->values->file->addError('required', $this->_(
                        'You must upload at least one file'
                    ));
                }

                return [];
            }

            $tempDir = $this->_getTempDir();
            $output = [];

            foreach ($fileNames as $fileName) {
                if (!$tempDir->hasFile($fileName)) {
                    $this->logs->logException(
                        Exceptional::{'df/core/fs/NotFound,TempNotFound'}(
                            'Couldn\'t find temp upload file: '.$tempDir->getPath().'/'.$fileName
                        )
                    );

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

    public function handlePostEvent(
        arch\node\IActiveForm $target,
        string $event,
        array $args
    ): void {
        $required = $this->_isRequired;
        $this->_isRequired = false;
        $this->onUploadEvent();
        $this->_isRequired = $required;
    }

    protected function onComplete()
    {
        if ($destination = $this->getStore('tempUploadDir')) {
            Atlas::deleteDir($destination);
        }

        link\http\upload\Handler::purgeUploadTemp();
    }
}
