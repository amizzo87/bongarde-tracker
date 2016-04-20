<?php

/** @var  \Herbert\Framework\Application $container */
/** @var  \Herbert\Framework\Http $http */
/** @var  \Herbert\Framework\Router $router */
/** @var  \Herbert\Framework\Enqueue $enqueue */
/** @var  \Herbert\Framework\Panel $panel */
/** @var  \Herbert\Framework\Shortcode $shortcode */
/** @var  \Herbert\Framework\Widget $widget */

use Illuminate\Database\Capsule\Manager as Capsule;
/*
if (!Capsule::schema()->hasTable('settings')) {
    Capsule::schema()->create('settings', function($table)
    {
        $table->increments('id');
        $table->string('product');
        $table->string('service_id');
        $table->string('sfUser');
        $table->string('sfPass');
        $table->string('sfToken');
        $table->string('cbEnv');
        $table->string('cbApiKey');

    });
}
*/
