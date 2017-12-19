<?php

namespace LaravelAntihackTool\PeskyCmf\CmfHackAttempts;

use LaravelAntihackTool\Antihack;
use PeskyCMF\Scaffold\DataGrid\ColumnFilter;
use PeskyCMF\Scaffold\DataGrid\DataGridColumn;
use PeskyCMF\Scaffold\Form\FormInput;
use PeskyCMF\Scaffold\NormalTableScaffoldConfig;

class CmfHackAttemptsScaffoldConfig extends NormalTableScaffoldConfig {

    protected $isDetailsViewerAllowed = false;
    protected $isCreateAllowed = false;
    protected $isEditAllowed = false;
    protected $isDeleteAllowed = true;

    static public function getTable() {
        return CmfHackAttemptsTable::getInstance();
    }

    static public function getMainMenuItem() {
        return [
            'label' => trans('antihack::antihack.hack_attempts.menu_title'),
            'url' => routeToCmfItemsTable('hack_attempts'),
            'icon' => 'fa fa-shield'
        ];
    }

    protected function createDataGridConfig() {
        return parent::createDataGridConfig()
            ->setOrderBy('id', 'desc')
            ->setColumns([
                'id',
                'ip',
                'user_agent',
                'reason' => DataGridColumn::create()
                    ->setValueConverter(function ($value) {
                        return $this->translate('reason.' . $value);
                    }),
                'created_at',
            ])
            ->closeFilterByDefault()
            ->setIsRowActionsColumnFixed(false)
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
                'reason' => ColumnFilter::create()
                    ->setInputType(ColumnFilter::INPUT_TYPE_MULTISELECT)
                    ->setAllowedValues(function () {
                        return $this->getReasonsOptions();
                    }),
                'created_at',
            ]);
    }

    protected function createItemDetailsConfig() {
        return parent::createItemDetailsConfig()
            ->setValueCells([
                'id',
                'ip',
                'user_agent',
                'reason',
                'extra',
                'created_at',
            ]);
    }
    
    protected function createFormConfig() {
        return parent::createFormConfig()
            ->setWidth(50)
            ->setFormInputs([
                'ip',
                'user_agent',
                'reason' => FormInput::create()
                    ->setType(FormInput::TYPE_SELECT)
                    ->setOptions(function () {
                        return $this->getReasonsOptions();
                    })
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

    protected function getReasonsOptions() {
        $reasons = CmfHackAttempt::getReasons();
        $options = [];
        foreach ($reasons as $reason) {
            $options[$reason] = $this->translate('reason.' . $reason);
        }
        return $options;
    }

    public function getCustomPage($pageName) {
        if ($pageName === 'blacklist') {
            return $this->getBlacklistPage();
        }
        return parent::getCustomPage($pageName);
    }

    public function getBlacklistPage() {
        return view('antihack::pages.blacklist', [
            'ipBlacklist' => Antihack::getBlacklistedIpAddresses(),
            'ipWhitelist' => Antihack::getWhitelistedIpAddresses(),
            'userAgentsBlacklist' => Antihack::getBlacklistedUserAgents()
        ]);
    }

}