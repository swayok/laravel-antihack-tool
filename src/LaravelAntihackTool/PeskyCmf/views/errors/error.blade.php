<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" content="noindex, nofollow"/>
    @yield('meta-tags')

    <title>@section('page-title'){{ trans("antihack::antihack.error_page.{$httpCode}.page_title") }}@show</title>
    <link href="/favicon.ico" type="image/x-icon" rel="shortcut icon"/>

    @yield('css-files')
    @yield('js-files')
</head>
<body class="@yield('body-class')">
<div style="margin-top: 200px; padding: 0 50px 0 50px; text-align: center" class="@yield('container-class')">
    @yield('logo')
    <h1 style="margin-bottom: 20px">
        @section('error-text'){!! trans("antihack::antihack.error_page.{$httpCode}.text") !!}@show
    </h1>
    @section('home-page-button')
        <a href="/" class="btn btn-primary">{{ trans('antihack::antihack.error_page.back_to_home_page') }}</a>
    @show
</div>
</body>
</html>
