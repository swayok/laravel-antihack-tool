<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use App\Db\AbstractRecord;

/**
 * @property-read int         $id
 * @property-read null|string $ip
 * @property-read null|string $user_agent
 * @property-read string      $created_at
 * @property-read string      $created_at_as_date
 * @property-read string      $created_at_as_time
 * @property-read int         $created_at_as_unix_ts
 *
 * @method $this    setId($value, $isFromDb = false)
 * @method $this    setIp($value, $isFromDb = false)
 * @method $this    setUserAgent($value, $isFromDb = false)
 * @method $this    setCreatedAt($value, $isFromDb = false)
 */
class CmfHackAttempt extends AbstractRecord {

    /**
     * @return CmfHackAttemptsTable
     */
    static public function getTable() {
        return CmfHackAttemptsTable::getInstance();
    }

}
