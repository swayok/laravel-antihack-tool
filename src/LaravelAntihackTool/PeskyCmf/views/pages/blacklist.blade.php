<?php
/**
 * @var array $ipBlacklist
 * @var array $ipWhitelist
 * @var array $userAgentsBlacklist
 */
?>

@include('cmf::ui.default_page_header', [
    'header' => cmfTransCustom('.page.medical_chests_reports.page_title'),
    'defaultBackUrl' => route('cmf_start_page'),
])
<div class="content">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="box">
                <div class="box-header">
                    <div class="box-title">Whitelisted IP Addresses</div>
                </div>
                <div class="box-body">

                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="box">
                <div class="box-header">
                    <div class="box-title">Blacklisted IP Addresses</div>
                </div>
                <div class="box-body">

                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="box">
                <div class="box-header">
                    <div class="box-title">Blacklisted User Agents (Regexp patterns)</div>
                </div>
                <div class="box-body">

                </div>
            </div>
        </div>
    </div>
</div>