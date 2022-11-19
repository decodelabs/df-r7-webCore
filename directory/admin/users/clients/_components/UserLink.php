<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_components;

use df\arch;

class UserLink extends arch\component\RecordLink
{
    protected $icon = 'user';
    protected $useNickName = false;
    protected $shortenName = false;


    // Name
    public function shouldUseNickName(bool $flag = null)
    {
        if ($flag !== null) {
            $this->useNickName = $flag;
            return $this;
        }

        return $this->useNickName;
    }

    public function shouldShortenName(bool $flag = null)
    {
        if ($flag !== null) {
            $this->shortenName = $flag;
            return $this;
        }

        return $this->shortenName;
    }


    // Name
    protected function getRecordName()
    {
        if ($this->useNickName) {
            $name = $this->record['nickName'];
        } else {
            $name = $this->record['fullName'];
        }

        if ($this->shortenName && preg_match('/^([^ ]+) ([^ ]+)$/', $name, $matches)) {
            $name = $matches[1] . ' ' . ucfirst($matches[2][0]) . '.';
        }

        if ($name === null) {
            $name = '#' . $this->record['id'];
        }

        return $name;
    }

    // Url
    protected function getRecordUri(string $id)
    {
        return '~admin/users/clients/details?user=' . $id;
    }
}
