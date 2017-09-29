<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use PeskyCMF\Scaffold\NormalTableScaffoldConfig;
use PeskyCMF\Scaffold\DataGrid\DataGridColumn;
use PeskyCMF\Scaffold\Form\FormInput;
use PeskyCMF\Scaffold\Form\InputRenderer;
use PeskyCMF\Scaffold\ItemDetails\ValueCell;

class CmfHackAttemptsScaffoldConfig extends NormalTableScaffoldConfig {

    protected $isDetailsViewerAllowed = false;
    protected $isCreateAllowed = false;
    protected $isEditAllowed = false;
    protected $isDeleteAllowed = true;

    static public function getTable() {
        return CmfHackAttemptsTable::getInstance();
    }

    protected function createDataGridConfig() {
        return parent::createDataGridConfig()
            ->setOrderBy('id', 'desc')
            ->setColumns([
                'id',
                'ip',
                'user_agent',
                'created_at',
            ])
            ->closeFilterByDefault()
            ->setMultiRowSelection(true)
            ->setIsBulkItemsDeleteAllowed(true)
            ->setIsFilteredItemsDeleteAllowed(true);
    }
    
    protected function createDataGridFilterConfig() {
        return parent::createDataGridFilterConfig()
            ->setFilters([
                'id',
                'ip',
                'user_agent',
                'created_at',
            ]);
    }

    protected function createItemDetailsConfig() {
        return parent::createItemDetailsConfig()
            ->setValueCells([
                'id',
                'ip',
                'user_agent',
                'created_at',
            ]);
    }
    
    protected function createFormConfig() {
        return parent::createFormConfig()
            ->setWidth(50)
            ->setFormInputs([
                'ip',
                'user_agent'
            ])
            ->setValidators(function () {
                return [
                    'ip' => 'nullable|string|ip'
                ];
            });
    }

    public function translate($section, $suffix = '', array $parameters = []) {
        return trans('antihack::antihack.hack_attempts.' . rtrim("{$section}.{$suffix}", '.'), $parameters);
    }
}