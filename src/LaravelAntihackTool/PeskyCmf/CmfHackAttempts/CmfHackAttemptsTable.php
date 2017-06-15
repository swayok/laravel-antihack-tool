<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use App\Db\AbstractTable;

class CmfHackAttemptsTable extends AbstractTable {

    /**
     * @return CmfHackAttemptsTableStructure
     */
    public function getTableStructure() {
        return CmfHackAttemptsTableStructure::getInstance();
    }

    /**
     * @return CmfHackAttempt
     */
    public function newRecord() {
        return new CmfHackAttempt();
    }

    /**
     * @return string
     */
    public function getTableAlias() {
        return 'CmfHackAttempts';
    }

}
