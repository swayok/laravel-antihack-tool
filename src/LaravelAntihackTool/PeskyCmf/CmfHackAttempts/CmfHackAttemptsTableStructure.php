<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use PeskyCMF\Db\Traits\IdColumn;
use PeskyORM\ORM\Column;
use PeskyORM\ORM\TableStructure;

/**
 * @property-read Column    $id
 * @property-read Column    $ip
 * @property-read Column    $user_agent
 * @property-read Column    $created_at
 */
class CmfHackAttemptsTableStructure extends TableStructure {

    use IdColumn;

    /**
     * @return string
     */
    static public function getTableName() {
        return config('antihack.table_name', 'hack_attempts');
    }

    static public function getConnectionName($writable) {
        return config('antihack.connection', parent::getConnectionName($writable));
    }

    private function ip() {
        return Column::create(Column::TYPE_STRING)
            ->convertsEmptyStringToNull();
    }

    private function user_agent() {
        return Column::create(Column::TYPE_STRING)
            ->convertsEmptyStringToNull();
    }

    private function created_at() {
        return Column::create(Column::TYPE_TIMESTAMP)
            ->disallowsNullValues()
            ->valueCannotBeSetOrChanged();
    }

}
