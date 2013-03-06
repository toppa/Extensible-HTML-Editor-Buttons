=== Extensible HTML Editor Buttons ===
Contributors: toppa
Donate link: http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin
Tags: post, admin, button, editor, jquery, quicktag
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.2.2
License: GPLv2 or later

A plugin for adding custom buttons to the WordPress HTML Editor, including custom modal dialogs.

== Description ==

Extensible HTML Editor Buttons enhances the WordPress HTML Editor button bar in 5 ways:

1. Gives you the option to replace the standard anchor and image buttons with new versions that provide modal input dialogs with more options (class, style, image width, height, etc.)
1. Adds two new buttons: div and span, each with their own modal input dialogs, for class, style, etc. attributes (you can disable them if you wish)
1. Provides a WYSIWYG settings form for adding your own custom buttons
1. Provides an example HTML file, which you can follow to add your own custom modal input dialogs for your custom buttons, for setting tag attributes such as style, class, or any attributes you specify
1. Provides an API for other plugins to add their own buttons and custom modal dialogs

It is multi-site compatible.

**Get Help**

Enter a post in [the wordpress.org support forum for Extensible HTML Editor Buttons](http://wordpress.org/support/plugin/extensible-html-editor-buttons), and I'll respond there.

**Give Help**

* Provide a language translation - [here's how](http://weblogtoolscollection.com/archives/2007/08/27/localizing-a-wordpress-plugin-using-poedit/)
* Fork [the Extensible HTML Editor Buttons repository on github](https://github.com/toppa/Extensible-HTML-Editor-Buttons) and make a code contribution
* If you're savvy user of the plugin, [answer questions in the support forum](http://wordpress.org/support/plugin/extensible-html-editor-buttons)
* If you tip your pizza delivery guy, tip your plugin developer - [make a donation](http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin/)

== Installation ==

1. Install Extensible HTML Editor Buttons in your plugin folder and activate.
1. Go to the Extensible HTML Editor Buttons settings menu to configure it, then edit a post or page using the HTML editor to see it in action
1. If you want to make a custom button with its own custom dialog form, look for the "custom-dialogs-example.html" file in the Extensible HTML Editor Buttons "display" directory, and follow the instructions in the comments.

== Frequently Asked Questions ==

Please go to <a href="http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin">the Extensible HTML Editor Buttons page for more information</a>.

== Screenshots ==

1. A custom modal dialog created with Extensible HTML Editor Buttons

== Changelog ==

= 1.2.2 = remove remaining overlooked dependencies on the Toppa Plugin Libraries plugin
= 1.2.1 = fix typos

= 1.2 =

* Use Toppa Libs locally, instead of loading from external plugin
* Rename "anchor" button to "link"
* Bug fix: correctly save enable/disable for externally registered buttons
* Bug fix: delete custom buttons when requested

= 1.1.4 = Documentation improvements

= 1.1.1 - 1.1.3 = Revisions to backup and restore code for custom dialogs, and added integration tests for it. Note this will not work on your first upgrade, since the old version does not have the pre-plugin update call that makes the backup copy.

= 1.1 =

* Backup and restore custom dialogs file when upgrading
* Support runtime upgrades (e.g via FTP without de/activate cycle on plugin menu)
* Support use in multisite networks
* Code cleanup: remove unneeded passing around of autoLoader object
* Code cleanup: remove causes of all PHP warnings

= 1.0.1 = Bug fix: Hide dialogs for inactive buttons

= 1.0 = First version
