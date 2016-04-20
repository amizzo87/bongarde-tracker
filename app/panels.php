<?php namespace BongardeTracker;

/** @var \Herbert\Framework\Panel $panel */
use BongardeTracker\Controllers\SettingsController;
/*
$panel->add([
    'type'   => 'wp-sub-panel',
    'parent' => 'options-general.php',
    'as'     => 'settingsSubpanel',
    'title'  => 'Bongarde Tracker',
    'slug'   => 'bongardetracker-dashboard',
    'uses'   => new SettingsController()
]);
*/
if( is_admin() )
    new SettingsController();