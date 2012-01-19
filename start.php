<?php
/*
Plugin Name: Extensible HTML Editor Buttons
Plugin URI: http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin/
Description: A plugin for adding custom buttons to the WordPress HTML Editor.
Author: Michael Toppa
Version: 1.0.1
Author URI: http://www.toppa.com
*/

$buttonableAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
register_activation_hook(__FILE__, 'buttonableActivate');
register_deactivation_hook(__FILE__, 'buttonableDeactivate');
load_plugin_textdomain('buttonable', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($buttonableAutoLoaderPath)) {
    require_once($buttonableAutoLoaderPath);
    $buttonableToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
    $buttonable = new Buttonable($buttonableAutoLoader);
    $buttonable->run();
}

function buttonableActivate() {
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

    if (!file_exists($autoLoaderPath)) {
        $message = __('To activate Extensible HTML Editor Buttons you need to first install', 'buttonable')
            . ' <a href="http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/">Toppa Plugins Libraries for WordPress</a>';
        buttonableCancelActivation($message);
    }

    elseif (!function_exists('spl_autoload_register')) {
        buttonableCancelActivation(__('You must have at least PHP 5.1.2 to use Extensible HTML Editor Buttons', 'buttonable'));
    }

    elseif (version_compare(get_bloginfo('version'), '3.1', '<')) {
        buttonableCancelActivation(__('You must have at least WordPress 3.1 to use Extensible HTML Editor Buttons', 'buttonable'));
    }

    else {
        require_once($autoLoaderPath);
        $toppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
        $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
        $buttonable = new Buttonable($buttonableAutoLoader);
        $status = $buttonable->install();

        if (is_string($status)) {
            buttonableCancelActivation($status);
        }
    }
}

function buttonableCancelActivation($message) {
    deactivate_plugins(basename(__FILE__));
    wp_die($message);
}

function buttonableDeactivate() {
}
