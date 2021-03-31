<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\postal\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

use DecodeLabs\Tagged as Html;
use DecodeLabs\Exceptional;

class AddressSelector extends arch\node\form\Delegate implements
    arch\node\ISelfContainedRenderableDelegate,
    arch\node\IResultProviderDelegate
{
    use arch\node\TForm_SelfContainedRenderableDelegate;
    use core\constraint\TRequirable;

    protected $_access = 'private';
    protected $_defaultMode = 'edit';
    protected $_autoComplete = true;
    protected $_address;

    protected $_countryList = null;
    protected $_isCountryListGrouped = false;


    public function setAccessLevel($level)
    {
        $level = strtolower($level);

        switch ($level) {
            case 'public':
            case 'protected':
            case 'private':
                $this->_access = $level;
                break;

            default:
                throw Exceptional::InvalidArgument(
                    $level.' is not a valid access level'
                );
        }

        return $this;
    }

    public function getAccessLevel()
    {
        return $this->_access;
    }

    public function setDefaultMode(?string $mode)
    {
        $this->_defaultMode = $mode;
        return $this;
    }

    public function getDefaultMode(): ?string
    {
        return $this->_defaultMode;
    }

    public function shouldAutoComplete(bool $flag=null)
    {
        if ($flag !== null) {
            $this->_autoComplete = $flag;
            return $this;
        }

        return $this->_autoComplete;
    }

    public function setAddress(apex\models\postal\address\Record $address=null)
    {
        $this->_address = $address;
        return $this;
    }

    public function getAddress()
    {
        return $this->_address;
    }

    public function setCountryList(array $countries=null, $grouped=null)
    {
        $this->_countryList = $countries;

        if ($grouped !== null) {
            $this->isCountryListGrouped($grouped);
        }

        return $this;
    }

    public function getCountryList()
    {
        return $this->_countryList;
    }

    public function isCountryListGrouped(bool $flag=null)
    {
        if ($flag !== null) {
            $this->_isCountryListGrouped = $flag;
            return $this;
        }

        return $this->_isCountryListGrouped;
    }



    protected function init()
    {
        if ($this->_state->hasStore('remove')) {
            $this->_address = null;
        }

        if (!$this->_address) {
            $this->_address = $this->data->getModel('postal')->address->newRecord();
        }

        if (!$this->_state->hasStore('mode')) {
            $this->_state->setStore('mode', $this->_defaultMode);
        }
    }



    protected function loadDelegates()
    {
        $this->loadDelegate('address', 'AddressEditor')
            ->setAddress($this->_address)
            ->setAccessLevel($this->_access)
            ->shouldAutoComplete($this->_autoComplete)
            ->isRequired($this->_isRequired)
            ->setCountryList($this->_countryList, $this->_isCountryListGrouped);
    }

    protected function setDefaultValues()
    {
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $fs)
    {
        $showEditor = true;

        // Look up postcode
        if ($this->_state->getStore('mode', 'lookup') == 'lookup') {
            $fa = $fs->addField($this->_('Find post code'))->push(
                $this->html->textbox(
                        $this->fieldName('postcodeSearch'),
                        $this->values->postcodeSearch
                    )
                    ->shouldAutoComplete(false),

                $this->html->eventButton(
                        $this->eventName('lookupPostcode'),
                        $this->_('Find')
                    )
                    ->setIcon('search')
            );

            $search = $this->values['postcodeSearch'];

            if (!empty($search)) {
                $showEditor = false;

                $fa->push(
                    $this->html->eventButton(
                            $this->eventName('clearSearch'),
                            $this->_('Clear')
                        )
                        ->setIcon('remove')
                );

                $model = $this->data->getModel('postal');
                $list = $model->address->lookupPostcodeAsList($search, $this->_access);

                if (empty($list)) {
                    $delegate = $this['address'];

                    if (empty($delegate->values['postcode'])) {
                        $delegate->values->postcode = $search;
                    }
                } else {
                    $fs->addField($this->_('Select address'))->push(
                        $this->html->select(
                                $this->fieldName('selectAddress'),
                                $this->values->selectAddress,
                                $list
                            )
                    );
                }
            } else {
                $fs->addField()->push(
                    Html::raw('<br />..or enter manually:<br />')
                );
            }
        }


        if ($showEditor) {
            $fs->push(
                $this['address']->renderFieldContent()
            );

            if ($this['address']->hasValue()) {
                $fs->addButtonArea(
                    $this->html->eventButton(
                                $this->eventName('removeAddress'),
                                $this->_('Remove address')
                            )
                            ->setIcon('remove')
                );
            }
        }
    }

    protected function onLookupPostcodeEvent()
    {
    }

    protected function onClearSearchEvent()
    {
        unset($this->values->selectAddress, $this->values->postcodeSearch);
    }

    protected function onRemoveAddressEvent()
    {
        $this->setStore('remove', true);
        $this['address']->getState()->reset();
    }

    public function apply()
    {
        if ($id = $this->values['selectAddress']) {
            return $id;
        }

        $output = $this['address']->apply();

        if ($output) {
            $output = $output->dereferenceDuplicate();
        }

        return $output;
    }
}
