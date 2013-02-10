<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$buttonablePath = dirname(__FILE__);
$buttonableParentDir = basename($buttonablePath);
$buttonableAutoLoaderPath = $buttonablePath . '/lib/buttonableAutoLoader.php';
require_once($buttonableAutoLoaderPath);
new ButtonableAutoLoader('/' . $buttonableParentDir . '/lib');
$buttonable = new Buttonable($buttonablePath . '/start.php');
$buttonableUninstallStatus = $buttonable->uninstall();

if ($buttonableUninstallStatus !== true) {
    deactivate_plugins($buttonablePath . '/start.php');
    wp_die($buttonableUninstallStatus);
}