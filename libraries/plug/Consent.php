<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\plug;

use df;
use df\core;
use df\plug;
use df\arch;
use df\aura;
use df\flex;

class Consent implements arch\IDirectoryHelper
{
    use arch\TDirectoryHelper;

    const COOKIE_NAME = 'cnx';

    const NECESSARY = 1;
    const PREFERENCES = 2;
    const STATISTICS = 4;
    const MARKETING = 8;

    protected static $_active;

    public function fetchUserRecord()
    {
        $currentData = $this->getCookieData();

        return $this->context->data->fetchOrCreateForAction(
            'axis://cookie/Consent',
            $currentData['id'] ?? null
        );
    }

    public function has(string $key)
    {
        $key = strtolower($key);

        switch ($key) {
            case 'preferences':
            case 'statistics':
            case 'marketing':
                return $this->getCookieData()[$key] ?? false;
        }

        return false;
    }

    public function getUserData(): array
    {
        return $this->getCookieData();
    }

    public function setUserData(array $data)
    {
        $currentData = $this->getCookieData();
        $currentId = $this->context->user->store->get('cnxId');

        if ($currentId !== null && $currentId !== $currentData['id']) {
            try {
                $this->context->data->cookie->consent->delete()
                    ->where('id', '=', $currentId)
                    ->execute();
            } catch (\Throwable $e) {
                core\logException($e);
            }
        }

        try {
            $record = $this->context->data->fetchOrCreateForAction(
                'axis://cookie/Consent',
                $currentData['id'] ?? null
            );

            if ($data['preferences'] ?? null) {
                $record['preferences'] = $record['preferences'] ?? 'now';
            } else {
                $record['preferences'] = null;
            }

            if ($data['statistics'] ?? null) {
                $record['statistics'] = $record['statistics'] ?? 'now';
            } else {
                $record['statistics'] = null;
            }

            if ($data['marketing'] ?? null) {
                $record['marketing'] = $record['marketing'] ?? 'now';
            } else {
                $record['marketing'] = null;
            }

            $record->save();
            $data['id'] = (string)$record['id'];
        } catch (\Throwable $e) {
            core\logException($e);
            $data['id'] = (string)flex\Guid::comb();
        }

        $this->context->user->store->set('cnxId', $data['id']);
        $this->setCookieData($data);
    }

    public function getCookieData(): array
    {
        if (self::$_active) {
            return self::$_active;
        }

        $output = [
            'id' => null,
            'version' => 0,
            'necessary' => false,
            'preferences' => false,
            'statistics' => false,
            'marketing' => false
        ];

        $raw = $this->context->http->getCookie('cnx');

        if ($raw === null || $raw === '1') {
            return $output;
        }

        $parts = explode('.', $raw);
        $output['version'] = (int)array_shift($parts);
        $flag = (int)array_shift($parts);
        $id = array_shift($parts);

        $output['necessary'] = true;
        $output['preferences'] = (bool)($flag & self::PREFERENCES);
        $output['statistics'] = (bool)($flag & self::STATISTICS);
        $output['marketing'] = (bool)($flag & self::MARKETING);

        if (!strlen($id)) {
            $id = null;
        }

        $output['id'] = $id;
        self::$_active = $output;
        return $output;
    }

    public function setCookieData(array $data)
    {
        $id = $data['id'] = $data['id'] ?? self::$_active['id'] ?? (string)flex\Guid::comb();
        $version = $data['version'] = (int)($data['version'] ?? 0);
        $data['preferences'] = (bool)($data['preferences'] ?? false);
        $data['statistics'] = (bool)($data['statistics'] ?? false);
        $data['marketing'] = (bool)($data['marketing'] ?? false);
        self::$_active = $data;

        $flag = self::NECESSARY;

        if ($data['preferences']) {
            $flag |= self::PREFERENCES;
        }

        if ($data['statistics']) {
            $flag |= self::STATISTICS;
        }

        if ($data['marketing']) {
            $flag |= self::MARKETING;
        }

        $cookie = $version.'.'.$flag.'.'.$id;
        $this->context->http->setCookie(self::COOKIE_NAME, $cookie)
            ->setExpiryDate(new core\time\Date('+1 years'));
    }
}
