<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-25 19:17
 */

function route($name, $parameters = [], $absolute = true)
{
    $appUrl = env('PROXY_PASS_URL'); // in your case: http://app.dev
    $appUrlSuffix = env('PROXY_PASS'); // in your case: subdirectory

    // Additional check, do the workaround only when a suffix is present and only when urls are absolute
    if ($appUrlSuffix && $absolute) {
        // Add the relative path to the app root url
        $relativePath = app('url')->route($name, $parameters, false);
        $url = $appUrl . $relativePath;
    } else {
        // This is the default behavior of route() you can find in laravel\vendor\laravel\framework\src\Illuminate\Foundation\helpers.php
        $url = app('url')->route($name, $parameters, $absolute);
    }

    return $url;
}