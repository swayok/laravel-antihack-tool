<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use PeskyORMLaravel\Db\TableStructureTraits\IdColumn;
use PeskyORM\ORM\Column;
use PeskyORM\ORM\TableStructure;

/**
 * @property-read Column    $id
 * @property-read Column    $ip
 * @property-read Column    $user_agent
 * @property-read Column    $reason
 * @property-read Column    $extra
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
        return config('antihack.connection') ?: parent::getConnectionName($writable);
    }

    private function ip() {
        return Column::create(Column::TYPE_STRING)
            ->convertsEmptyStringToNull();
    }

    private function user_agent() {
        return Column::create(Column::TYPE_STRING)
            ->convertsEmptyStringToNull();
    }

    private function reason() {
        return Column::create(Column::TYPE_STRING)
            ->convertsEmptyStringToNull();
    }

    private function extra() {
        return Column::create(Column::TYPE_JSONB)
            ->convertsEmptyStringToNull()
            ->setDefaultValue('{}');
    }

    private function created_at() {
        return Column::create(Column::TYPE_TIMESTAMP)
            ->disallowsNullValues()
            ->valueCannotBeSetOrChanged();
    }

}
