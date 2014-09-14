<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\geoIp\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\link;

class HttpMaxMindDb extends arch\form\Action {
    
    protected $_config;

    protected function _init() {
        $this->_config = link\geoIp\Config::getInstance();
    }

    protected function _setDefaultValues() {
        $this->values->isEnabled = $this->_config->isEnabled();
        $this->values->file = $this->_config->values->adapters->MaxMindDb['file'];
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component('~admin/system/geo-ip/IndexHeaderBar')
        );

        $fileList = $this->_getFileList();

        $form = $this->content->addForm();
        $form->setEncoding($form::ENC_MULTIPART);
        $fs = $form->addFieldSet($this->_('MaxMind DB adapter'));

        // Set as default
        if($this->_config->getDefaultAdapter() != 'MaxMindDb') {
            $fs->addFieldArea()->push(
                $this->html->checkbox('setAsDefault', $this->values->setAsDefault, $this->_(
                    'Make this the default Geo IP adapter'
                ))
            );
        }

        // Enabled
        $fs->addFieldArea($this->_('Geo IP usage'))->push(
            $this->html->radioButtonGroup('isEnabled', $this->values->isEnabled, [
                    '1' => $this->_('Enabled'),
                    '0' => $this->_('Disabled')
                ])
                ->isRequired(true)
                ->isDisabled(empty($fileList))
        );

        if(empty($fileList)) {
            $fs->push($this->html->hidden('isEnabled', false));
        }


        $fs = $form->addFieldSet($this->_('Databases'));
        $fa = $fs->addFieldArea($this->_('File'));

        if(empty($fileList)) {
            $fa->addFlashMessage($this->_(
                'There are currently no database files to choose from'
            ), 'warning');
        } else {
            $fa->push(
                $this->html->selectList('file', $this->values->file, $fileList)
                    ->isRequired(true)
            );
        }

        $fs->addFieldArea($this->_('Upload file'))->setDescription($this->_(
            '.mmdb or .mmdb.gz files only please'
        ))->push(
            $this->html->fileUpload('upload', $this->values->upload),
            $this->html->eventButton('upload', $this->_('Upload'))
                ->setIcon('upload')
                ->setDisposition('positive')
        );

        $hasLiteCountry = isset($fileList['GeoLite2-Country.mmdb']);
        $hasLiteCity = isset($fileList['GeoLite2-City.mmdb']);

        if(!$hasLiteCountry || !$hasLiteCity) {
            $fa = $fs->addFieldArea($this->_('Fetch free databases'))
                ->setErrorContainer($this->values->fetch);

            $fa->addFlashMessage($this->_(
                'Please be patient while files download, it can take a little while!'
            ));

            if(!$hasLiteCountry) {
                $fa->push(
                    $this->html->eventButton(
                            'fetchLiteCountry',
                            $this->_('GeoLite2-Country')
                        )
                        ->setIcon('download')
                        ->setDisposition('positive')
                );
            }

            if(!$hasLiteCity) {
                $fa->push(
                    $this->html->eventButton(
                            'fetchLiteCity',
                            $this->_('GeoLite2-City')
                        )
                        ->setIcon('download')
                        ->setDisposition('positive')
                );
            }
        }

        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }

    protected function _getFileList() {
        $output = [];
        $dir = $this->application->getLocalStoragePath().'/geoIp/';

        foreach(core\io\Util::listFilesIn($dir) as $name) {
            if(substr($name, -5) != '.mmdb') {
                continue;
            }

            $output[$name] = substr($name, 0, -5);
        }

        return $output;
    }

    protected function _onUploadEvent() {
        $uploadHandler = new link\http\upload\Handler();
        $uploadHandler->setAllowedExtensions(['mmdb', 'gz']);
        $targetPath = null;
        $path = $this->application->getLocalStoragePath().'/geoIp';

        if(count($uploadHandler)) {
            foreach($uploadHandler as $file) {
                if($file->getExtension() == 'gz') {
                    $file->tempUpload($this->values->upload);

                    if($this->values->upload->isValid()) {
                        $targetPath = $this->_extractGz($file->getTempPath(), $path.'/'.$file->getFileName());
                    }
                } else {
                    $file->upload($path, $this->values->upload);

                    if($this->values->upload->isValid()) {
                        $targetPath = $file->getDestinationPath();
                    }
                }
            }
        }

        if($targetPath) {
            $this->values->file = basename($targetPath);
            $this->values->isEnabled = true;
        }
    }

    protected function _onFetchLiteCountryEvent() {
        $url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz';
        $this->_fetchUrl($url);
    }

    protected function _onFetchLiteCityEvent() {
        $url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
        $this->_fetchUrl($url);
    }

    protected function _fetchUrl($url) {
        $fileName = basename($url);
        $path = $this->application->getLocalStoragePath().'/geoIp';
        core\io\Util::ensureDirExists($path);

        if(is_file($path.'/'.substr($fileName, 0, -3))) {
            return;
        }

        if(!is_file($path.'/'.$fileName)) {
            try {
                set_time_limit(0);
                file_put_contents($path.'/'.$fileName, fopen($url, 'r'));
            } catch(\ErrorException $e) {
                $this->values->fetch->addError('download', $this->_(
                    'File download failed!'
                ));

                return;
            }
        }
        
        $targetPath = $this->_extractGz($path.'/'.$fileName);
        $this->values->file = basename($targetPath);
        $this->values->isEnabled = true;
    }

    protected function _extractGz($path, $targetPath=null) {
        // TODO: replace with proper archive lib code
        $bufferSize = 4096; 

        if($targetPath === null) {
            $targetPath = substr($path, 0, -3);
        }

        core\io\Util::ensureDirExists(dirname($targetPath));

        $gfp = gzopen($path, 'rb');
        $tfp = fopen($targetPath, 'wb');

        while(!gzeof($gfp)) {
            fwrite($tfp, gzread($gfp, $bufferSize));
        }

        fclose($tfp);
        gzclose($gfp);
        unlink($path);
        return $targetPath;
    }

    protected function _onSaveEvent() {
        $validator = $this->data->newValidator()

            // Default
            ->addField('setAsDefault', 'boolean')
                ->end()

            // Enabled
            ->addField('isEnabled', 'boolean')
                ->end()

            // File
            ->addField('file', 'enum')
                ->setOptions(array_keys($this->_getFileList()))
                ->end()

            ->validate($this->values);

        if($this->isValid()) {
            if($validator['setAsDefault']) {
                $this->_config->setDefaultAdapter('MaxMindDb');
            }

            $this->_config->values->adapters->MaxMindDb->file = $validator['file'];
            $enabled = (bool)$validator['isEnabled'];

            if($enabled && !link\geoIp\Handler::isAdapterAvailable('MaxMindDb')) {
                $enabled = false;
            }

            $this->_config->isEnabled($enabled);
            $this->_config->save();

            $this->comms->flash(
                'mmdb.settings',
                $this->_('The MaxMind DB settings have been successfully updated'),
                'success'
            );

            return $this->complete();
        }
    }
}