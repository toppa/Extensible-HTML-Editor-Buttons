=== Extensible HTML Editor Buttons ===
Contributors: toppa
Donate link: http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin
Tags: post, admin, button, editor, jquery, quicktag
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: 1.1

A plugin for adding custom buttons to the WordPress HTML Editor, including custom modal dialogs.

== Description ==

Extensible HTML Editor Buttons enhances the WordPress HTML Editor button bar in 5 ways:

1. Provides a WYSIWYG settings form for adding your own custom buttons
1. Provides an example HTML file, which you can follow to add your own custom modal input dialogs for your custom buttons, for setting tag attributes such as style, class, or any attributes you specify
1. Adds two new buttons: div and span, each with their own modal input dialogs, for class, style, etc. attributes (you can disable then if you wish)
1. Gives you the option to replace the standard anchor and image buttons with new versions that provide modal input dialogs with more options (class, style, image width, height, etc.)
1. Provides an API for other plugins to add their own buttons and custom modal dialogs

**Installation of [Toppa Plugin Libraries for WordPress](http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/) is required for this plugin. Please download and install it first.**

A translation file is included for language localization. If you are bilingual, please contribute a translation!

**Installation Instructions**

1. Install the [Toppa Plugin Libraries for WordPress](http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/) plugin
1. Install Extensible HTML Editor Buttons in your plugin folder and activate.
1. Go to the Extensible HTML Editor Buttons settings menu to configure it, then edit a post or page using the HTML editor to see it in action


== Frequently Asked Questions ==

Please go to <a href="http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin">the Extensible HTML Editor Buttons page for more information</a>.

== Screenshots ==

1. A custom modal dialog created with Extensible HTML Editor Buttons

== Changelog ==

= 1.1 =

* Backup and restore custom dialogs file when upgrading
* Support runtime upgrades (e.g via FTP with de/activate cycle on plugin menu)
* Support use in multisite networks
* Code cleanup: remove unneeded passing around of autoLoader object
* Code cleanup: remove all PHP warnings

= 1.0.1 = Bug fix: Hide dialogs for inactive buttons

= 1.0 = First version