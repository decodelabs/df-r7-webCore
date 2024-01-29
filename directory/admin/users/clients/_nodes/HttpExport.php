<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\clients\_nodes;

use DecodeLabs\Dictum;
use DecodeLabs\R7\Legacy;
use df\arch;

class HttpExport extends arch\node\Form
{
    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet('Export users');

        // Countries
        $countries = $this->data->user->client->selectDistinct('country')
            ->toList('country');


        $fs->addField('Countries')->setDescription(
            'Leave empty for all countries'
        )->push(
            $this->html->checkboxGroup('countries', $this->values->countries, $this->i18n->countries->getList($countries))
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $val = $this->data->newValidator()
            ->addField('countries', 'idList')
            ->validate($this->values);

        if (!$this->isValid()) {
            return;
        }

        $this->complete();

        $countries = $val['countries'];
        $fileName = 'users-';

        if (!empty($countries)) {
            if (count($countries) < 10) {
                $fileName .= implode('-', $countries) . '-';
            } else {
                $fileName .= 'multiple-countries-';
            }
        }

        $fileName .= date('Y-m-d') . '.csv';

        return Legacy::$http->csvGenerator($fileName, function ($builder) use ($countries) {
            $query = $this->data->user->client->select()

                ->chainIf($countries !== null, function ($query) use ($countries) {
                    $query->where('country', 'in', $countries);
                })

                ->orderBy('loginDate DESC');


            $builder->setFields([
                'id' => 'User ID',
                'name' => 'Name',
                'email' => 'Email',
                'country' => 'Country',
                'joinDate' => 'Registration date',
                'loginDate' => 'Last login date'
            ]);

            if ($countries !== null) {
                $builder->addInfoRow([
                    'Countries:',
                    implode(', ', $this->i18n->countries->getList($countries))
                ]);
                $builder->addInfoRow([]);
            }

            foreach ($query as $user) {
                $builder->addRow([
                    'id' => (string)$user['id'],
                    'name' => $user['fullName'],
                    'email' => $user['email'],
                    'country' => $this->i18n->countries->getName($user['country']),
                    'joinDate' => Dictum::$time->format($user['joinDate'], 'Y-m-d'),
                    'loginDate' => Dictum::$time->format($user['loginDate'], 'Y-m-d')
                ]);
            }
        });
    }
}
