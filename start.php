<?php
/*
Plugin Name: Extensible HTML Editor Buttons
Plugin URI: http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin/
Description: A plugin for adding custom buttons to the WordPress HTML Editor.
Author: Michael Toppa
Version: 1.2.2
Author URI: http://www.toppa.com
*/

$buttonablePath = dirname(__FILE__);
$buttonableParentDir = basename($buttonablePath);
$buttonableAutoLoaderPath = $buttonablePath . '/lib/ButtonableAutoLoader.php';

add_action('wpmu_new_blog', 'buttonableActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'buttonableActivate');
load_plugin_textdomain('buttonable', false, $buttonableParentDir . '/languages/');

if (file_exists($buttonableAutoLoaderPath)) {
    require_once($buttonableAutoLoaderPath);
    new ButtonableAutoLoader('/' . $buttonableParentDir . '/lib');
    $buttonable = new Buttonable(__FILE__);
    $buttonable->run();
}

function buttonableActivateForNewNetworkSite($blog_id) {
    global $wpdb;

    if (is_plugin_active_for_network(__FILE__)) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        buttonableActivate();
        switch_to_blog($old_blog);
    }
}

function buttonableActivate() {
    $status = buttonableActivationChecks();

    if (is_string($status)) {
        buttonableCancelActivation($status);
    }
}

// this is also used by ButtonableInstall when doing a runtime upgrade
function buttonableActivationChecks() {
    if (!function_exists('spl_autoload_register')) {
        $status = __('You must have at least PHP 5.1.2 to use Extensible HTML Editor Buttons', 'buttonable');
    }

    elseif (version_compare(get_bloginfo('version'), '3.0', '<')) {
        $status = __('You must have at least WordPress 3.0 to use Extensible HTML Editor Buttons', 'buttonable');
    }

    else {
        $status = true;
    }

    return $status;
}

function buttonableCancelActivation($message) {
    deactivate_plugins(__FILE__);
    wp_die($message);
}

