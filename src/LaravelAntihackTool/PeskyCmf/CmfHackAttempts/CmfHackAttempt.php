<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use App\Db\AbstractRecord;
use LaravelAntihackTool\Antihack;

/**
 * @property-read int         $id
 * @property-read null|string $ip
 * @property-read null|string $user_agent
 * @property-read null|string $reason
 * @property-read string      $extra
 * @property-read array       $extra_as_array
 * @property-read object      $extra_as_object
 * @property-read string      $created_at
 * @property-read string      $created_at_as_date
 * @property-read string      $created_at_as_time
 * @property-read int         $created_at_as_unix_ts
 *
 * @method $this    setId($value, $isFromDb = false)
 * @method $this    setIp($value, $isFromDb = false)
 * @method $this    setReason($value, $isFromDb = false)
 * @method $this    setExtra($value, $isFromDb = false)
 * @method $this    setCreatedAt($value, $isFromDb = false)
 */
class CmfHackAttempt extends AbstractRecord {

    /**
     * @return CmfHackAttemptsTable
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    static public function getTable() {
        return CmfHackAttemptsTable::getInstance();
    }

    static public function getReasons() {
        return [
            Antihack::REASON_BAD_USER_AGENT,
            Antihack::REASON_INVALID_IP_ADDRESS,
            Antihack::REASON_PHP_EXTENSION_IN_URL,
            Antihack::REASON_BAD_URL,
            Antihack::REASON_BAD_URL_QUERY_DATA,
            Antihack::REASON_BAD_POST_DATA,
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isFromDb
     * @return $this
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     * @throws \PeskyORM\Exception\InvalidDataException
     */
    public function setUserAgent($value, $isFromDb = false) {
        return $this->updateValue('user_agent', mb_substr((string)$value, 0, 254), $isFromDb);
    }

}
