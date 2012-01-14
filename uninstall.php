<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once(dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

$buttonableToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
$buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
$buttonable = new Buttonable($buttonableAutoLoader);
$buttonableUninstallStatus = $buttonable->uninstall();

if ($buttonableUninstallStatus !== true) {
    deactivate_plugins(basename(__FILE__));
    wp_die($buttonableUninstallStatus);
}