<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\plug;

use DecodeLabs\Guidance;
use DecodeLabs\R7\Legacy;
use df\arch;
use df\core;

class Consent implements arch\IDirectoryHelper
{
    use arch\TDirectoryHelper;

    public const COOKIE_NAME = 'cnx';

    public const NECESSARY = 1;
    public const PREFERENCES = 2;
    public const STATISTICS = 4;
    public const MARKETING = 8;

    protected static $_active;
    protected static $_enabled = null;

    public function fetchUserRecord()
    {
        $currentData = $this->getCookieData();

        return $this->context->data->fetchOrCreateForAction(
            'axis://cookie/Consent',
            $currentData['id'] ?? null
        );
    }

    public function setEnabled(bool $flag): void
    {
        self::$_enabled = $flag;
    }

    public function isEnabled(): bool
    {
        if (self::$_enabled === null) {
            try {
                self::$_enabled = (bool)$this->context->apex->getTheme()->getFacet('cookieNotice');
            } catch (\Exception $e) {
                self::$_enabled = false;
                core\logException($e);
            }
        }

        return self::$_enabled;
    }

    public function has(string ...$keys)
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $data = $this->getUserData();

        foreach ($keys as $key) {
            $key = strtolower((string)$key);

            switch ($key) {
                case 'preferences':
                case 'statistics':
                case 'marketing':
                    if (!($data[$key] ?? false)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    public function getId(): ?string
    {
        return $this->getUserData()['id'] ?? null;
    }

    public function getUserData(string $logId = null): array
    {
        if (self::$_active) {
            return self::$_active;
        }

        $output = $this->getCookieData();

        if ($output['id'] === null && $logId !== null) {
            $record = $this->context->data->cookie->consent->select()
                ->where('id', '=', $logId)
                ->toRow();

            if ($record) {
                $output = [
                    'id' => (string)$record['id'],
                    'version' => 0,
                    'necessary' => true,
                    'preferences' => (bool)$record['preferences'],
                    'statistics' => (bool)$record['statistics'],
                    'marketing' => (bool)$record['marketing']
                ];
            } else {
                $output['id'] = $logId;
            }
        }

        self::$_active = $output;
        return $output;
    }

    public function setUserData(array $data)
    {
        $currentData = $this->getCookieData();
        $currentId = $this->context->user->store->get('cnxId') ?? $data['id'] ?? null;

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
                $currentData['id'] ?? $data['id'] ?? null
            );

            if ($record->isNew() && isset($data['id'])) {
                $record['id'] = $data['id'];
            }

            $record['preferences'] = $data['preferences'] ?? false;
            $record['statistics'] = $data['statistics'] ?? false;
            $record['marketing'] = $data['marketing'] ?? false;

            $record->save();
            $data['id'] = (string)$record['id'];
        } catch (\Throwable $e) {
            core\logException($e);
            $data['id'] = Guidance::createV4CombString();
        }

        $this->context->user->store->set('cnxId', $data['id']);
        $this->setCookieData($data);
    }

    public function getCookieData(): array
    {
        $output = [
            'id' => null,
            'version' => 0,
            'necessary' => false,
            'preferences' => false,
            'statistics' => false,
            'marketing' => false
        ];

        $raw = Legacy::$http->getCookie('cnx');

        if ($raw === null || $raw === '1') {
            return $output;
        }

        $parts = explode('.', $raw);
        $output['version'] = (int)array_shift($parts);
        $flag = (int)array_shift($parts);
        $id = (string)array_shift($parts);

        $output['necessary'] = true;
        $output['preferences'] = (bool)($flag & self::PREFERENCES);
        $output['statistics'] = (bool)($flag & self::STATISTICS);
        $output['marketing'] = (bool)($flag & self::MARKETING);

        if (!strlen($id)) {
            $id = null;
        }

        $output['id'] = $id;
        return $output;
    }

    public function setCookieData(array $data)
    {
        $id = $data['id'] = $data['id'] ?? self::$_active['id'] ?? Guidance::createV4CombString();
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

        $cookie = $version . '.' . $flag . '.' . $id;

        Legacy::$http->setCookie(self::COOKIE_NAME, $cookie)
            ->setExpiryDate(new core\time\Date('+1 years'));
    }
}
