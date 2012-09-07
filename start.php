<?php
/*
Plugin Name: Extensible HTML Editor Buttons
Plugin URI: http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin/
Description: A plugin for adding custom buttons to the WordPress HTML Editor.
Author: Michael Toppa
Version: 1.1.3
Author URI: http://www.toppa.com
*/

$buttonableAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
add_action('wpmu_new_blog', 'buttonableActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'buttonableActivate');
load_plugin_textdomain('buttonable', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($buttonableAutoLoaderPath)) {
    require_once($buttonableAutoLoaderPath);
    $buttonableToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
    $buttonable = new Buttonable();
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
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

    $status = buttonableActivationChecks();

    if (is_string($status)) {
        buttonableCancelActivation($status);
    }

    require_once($autoLoaderPath);
    $toppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
    $buttonable = new Buttonable();
    $status = $buttonable->install();

    if (is_string($status)) {
        buttonableCancelActivation($status);
        return null;
    }

    return null;
}

function buttonableActivationChecks() {
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
    $toppaLibsVersion = get_option('toppaLibsVersion');

    if (!file_exists($autoLoaderPath) || !$toppaLibsVersion || version_compare($toppaLibsVersion, '1.3.6', '<')) {
        return __('To activate Extensible HTML Editor Buttons you need to have the current version of', 'buttonable')
            . ' <a href="plugin-install.php?tab=plugin-information&plugin=toppa-plugin-libraries-for-wordpress">Toppa Plugins Libraries for WordPress</a>. '
            . __('Click the link to view details, and then click the "Install Now" button to get the current version. Then you can activate Extensible HTML Editor Buttons.', 'buttonable');
    }

    if (!function_exists('spl_autoload_register')) {
        return __('You must have at least PHP 5.1.2 to use Extensible HTML Editor Buttons', 'buttonable');
    }

    if (version_compare(get_bloginfo('version'), '3.1', '<')) {
        return __('You must have at least WordPress 3.1 to use Extensible HTML Editor Buttons', 'buttonable');
    }

    return true;
}
function buttonableCancelActivation($message) {
    deactivate_plugins('extensible-html-editor-buttons/start.php');
    wp_die($message);
}

