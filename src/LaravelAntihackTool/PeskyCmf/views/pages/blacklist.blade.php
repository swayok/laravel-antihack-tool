<?php
/**
 * @var \PeskyCMF\Scaffold\ScaffoldConfig $scaffoldConfig
 * @var array $ipBlacklist
 * @var array $ipBlacklistByConfig
 * @var array $ipWhitelist
 * @var array $userAgentsBlacklist
 */
?>

@include('cmf::ui.default_page_header', [
    'header' => $scaffoldConfig->translate('blacklist_page', 'page_title'),
    'defaultBackUrl' => routeToCmfItemsTable($scaffoldConfig::getResourceName()),
])
<div class="content">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="box box-solid box-success">
                <div class="box-header">
                    <div class="box-title">{{ $scaffoldConfig->translate('blacklist_page', 'whitelisted_ips') }}</div>
                </div>
                <div class="box-body">
                    @foreach($ipWhitelist as $ip)
                        {{ $ip }}<br>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="box box-solid box-warning">
                <div class="box-header">
                    <div class="box-title">{{ $scaffoldConfig->translate('blacklist_page', 'blacklisted_ips_in_config') }}</div>
                </div>
                <div class="box-body">
                    @foreach($ipBlacklistByConfig as $ip)
                        {{ $ip }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="box box-solid box-danger">
                <div class="box-header">
                    <div class="box-title">{{ $scaffoldConfig->translate('blacklist_page', 'blacklisted_ips') }}</div>
                </div>
                <div class="box-body">
                    @foreach($ipBlacklist as $ip)
                        {{ $ip }}<br>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="box box-solid box-danger">
                <div class="box-header">
                    <div class="box-title">{{ $scaffoldConfig->translate('blacklist_page', 'blacklisted_user_agents') }}</div>
                </div>
                <div class="box-body">
                    @foreach($userAgentsBlacklist as $ip)
                        {{ $ip }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>