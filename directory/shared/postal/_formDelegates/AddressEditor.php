<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\postal\_formDelegates;

use df\core;
use df\apex;
use df\arch;

use df\aura\html\widget\FieldSet as FieldSetWidget;
use df\apex\models\postal\address\Record as AddressRecord;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;

class AddressEditor extends arch\node\form\Delegate
{
    use core\constraint\TRequirable;

    protected $_access = 'private';
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

    protected function init(): void
    {
        if (!$this->_address) {
            $this->_address = $this->data->getModel('postal')->address->newRecord([
                'owner' => Disciple::getId(),
                'access' => $this->_access
            ]);

            return;
        }

        if ($this->_address->mustSpawnToEdit($this->_access, Disciple::getId())) {
            $this->_address = $this->_address->spawnNew([
                'owner' => Disciple::getId(),
                'access' => $this->_access
            ]);
        }
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_address, [
            'street1', 'street2', 'street3',
            'city', 'county', 'country', 'postcode'
        ]);

        if (empty($this->values['country'])) {
            $this->values->country = $this->i18n->getDefaultLocale()->getRegion();
        }
    }

    public function renderFieldSet(mixed $legend=null): FieldSetWidget
    {
        if ($legend === null) {
            $legend = $this->_('Address');
        }

        return $this->html->fieldSet($legend)->push(
            $this->renderFieldContent()
        );
    }

    public function renderFieldContent()
    {
        $output = $this->html->elementContentContainer();

        // Street 1
        $output->push($this->html->field($this->_('Street line 1'))->push(
            $this->html->textbox(
                    $this->fieldName('street1'),
                    $this->values->street1
                )
                ->shouldAutoComplete($this->_autoComplete)
                ->isRequired($this->_isRequired)
        ));

        // Street 2
        $output->push($this->html->field($this->_('Street line 2'))->push(
            $this->html->textbox(
                    $this->fieldName('street2'),
                    $this->values->street2
                )
                ->shouldAutoComplete($this->_autoComplete)
        ));

        // Street 3
        $output->push($this->html->field($this->_('Street line 3'))->push(
            $this->html->textbox(
                    $this->fieldName('street3'),
                    $this->values->street3
                )
                ->shouldAutoComplete($this->_autoComplete)
        ));

        // City
        $output->push($this->html->field($this->_('Town / city'))->push(
            $this->html->textbox(
                    $this->fieldName('city'),
                    $this->values->city
                )
                ->shouldAutoComplete($this->_autoComplete)
                ->isRequired($this->_isRequired)
        ));

        // County
        $output->push($this->html->field($this->_('County / region'))->push(
            $this->html->textbox(
                    $this->fieldName('county'),
                    $this->values->county
                )
                ->shouldAutoComplete($this->_autoComplete)
        ));

        // Postcode
        $output->push($this->html->field($this->_('Post code'))->push(
            $this->html->textbox(
                    $this->fieldName('postcode'),
                    $this->values->postcode
                )
                ->shouldAutoComplete($this->_autoComplete)
                ->isRequired($this->_isRequired)
        ));

        // Country
        if ($this->_countryList !== null) {
            $countries = $this->_countryList;
            $isGrouped = $this->_isCountryListGrouped;
        } else {
            $countries = $this->i18n->countries->getList();
            $isGrouped = false;
        }

        $output->push($this->html->field($this->_('Country'))->push(
            $this->html->{$isGrouped ? 'groupedSelect' : 'select'}(
                    $this->fieldName('country'),
                    $this->values->country,
                    $countries
                )
                ->isRequired($this->_isRequired)
        ));

        return $output;
    }

    public function hasValue(): bool
    {
        return $this->values->hasAnyValue(['street1', 'street2', 'street3', 'city', 'county', 'postcode']);
    }

    public function apply(): ?AddressRecord
    {
        if (!$this->hasValue()) {
            if ($this->_address->isNew()) {
                return null;
            } else {
                return $this->_address;
            }
        }


        $this->data->newValidator()

            // Street 1
            ->addRequiredField('street1', 'text')

            // Street 2
            ->addField('street2', 'text')

            // Street 3
            ->addField('street3', 'text')

            // City
            ->addRequiredField('city', 'text')

            // County
            ->addField('county', 'text')

            // Postcode
            ->addRequiredField('postcode', 'text')
                ->setSanitizer(function ($value) {
                    return strtoupper($value);
                })

            // Country
            ->addRequiredField('country', 'text')
                ->setSanitizer(function ($value) {
                    return strtoupper($value);
                })
                ->extend(function ($value, $field) {
                    if (!$this->i18n->countries->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid country code'
                        ));
                    }
                })

            ->validate($this->values)
            ->applyTo($this->_address);

        if ($this->_address->mustSpawnOnSave($this->_access)) {
            $this->_address = $this->_address->spawnNew([
                'access' => $this->_access
            ]);
        }

        return $this->_address;
    }
}
