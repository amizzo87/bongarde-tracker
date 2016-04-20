<?php namespace BongardeTracker;

/** @var \Herbert\Framework\API $api */

/**
 * Gives you access to the Helper class from Twig
 * {{ MyPlugin.helper('assetUrl', 'icon.png') }}
 */

use BongardeTracker\Controllers\TrackerController;

$api->add('helper', function ()
{
    $args = func_get_args();
    $method = array_shift($args);

    return forward_static_call_array(__NAMESPACE__ . '\\Helper::' . $method, $args);
});

$api->add('test', function()
{
    $current_user = wp_get_current_user();
    return 'testing api: ' . $current_user->display_name;
});

$api->add('TotangoCore', function()
{
    $current_user = wp_get_current_user();
    return (new TrackerController($current_user))->TotangoCore();

});
