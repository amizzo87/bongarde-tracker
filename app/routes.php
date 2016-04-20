<?php namespace BongardeTracker;

/** @var \Herbert\Framework\Router $router */

$router->get([
    'as'   => 'totango',
    'uri'  => '/totango.js',
    'uses' => __NAMESPACE__ . '\Controllers\TrackerController@Totango'

]);