<?php

/**
 * @wordpress-plugin
 * Plugin Name:       BongardeTracker
 * Plugin URI:        N/A
 * Description:       A plugin for tracking.
 * Version:           1.0.0
 * Author:            Anthony Izzo
 * Author URI:        N/A
 * License:           To kill.
 * GitHub Plugin URI: amizzo87/bongarde-tracker
 */
use Bugsnag_Client as Bugsnag_Client;
require_once ABSPATH . '/sforce/soapclient/SforcePartnerClient.php';
require_once ABSPATH . '/sforce/soapclient/SforceEnterpriseClient.php';
require_once __DIR__ . '/vendor/bugsnag/bugsnag/src/Bugsnag/Autoload.php';
ini_set("soap.wsdl_cache_enabled", "0");

define("USERNAME", get_option( 'bongarde_tracker_options' )['sf_login']);
define("PASSWORD", get_option( 'bongarde_tracker_options' )['sf_pass']);
define("SECURITY_TOKEN", get_option( 'bongarde_tracker_options' )['sf_token']);

$bugsnag = new Bugsnag_Client(get_option( 'bongarde_tracker_options')['bugsnag_api']);
set_error_handler(array($bugsnag, 'errorHandler'));
set_exception_handler(array($bugsnag, 'exceptionHandler'));

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/getherbert/framework/bootstrap/autoload.php';
// require_once ABSPATH . 'wp-content/plugins/chargebee/lib/ChargeBee.php';

// include_once __DIR__ . '/resources/assets/php/totango.php';
// ChargeBee_Environment::configure("{site}","{site_api_key}");