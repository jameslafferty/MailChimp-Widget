=== MailChimp Widget ===
Contributors: jameslafferty
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JSL4JTA4KMZLG
Tags: newsletter, MailChimp, mailing list, widget, email marketing
Requires at least: 4.7.5
Tested up to: 4.7.5
Stable tag: 0.8.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a widget to let your users sign up to your MailChimp email marketing lists.

== Description ==

Long description

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Enter a valid MailChimp API key on the plugin admin page ("Settings" >> "MailChimp Widget"). You can obtain a MailChimp API key after creating an account at MailChimp.com.
* Drag the widget into your sidebar from the "Widgets" ("Appearance" >> "Widgets") menu in WordPress.
* Select a mailing list and you're ready to go!
* Please rate the plugin.

== Screenshots ==

1. Image 01.
2. Image 02.

== Changelog ==

= 1.0.0 =
* Full plugin rewrite. These changes _are_ breaking to earlier versions of the plugin.
* Calls updated MailChimp REST API.
* Adds support for optional merge fields, and automatically includes all required merge fields per list.
* Makes hiding the widget on successful signup optional.
* Improves form markup.

= 0.8.2 =
* Added Spanish translation. Thank you to [Iván Gabriel Campaña Naranjo](http://icampana.blogspot.com/) for this contribution!

= 0.8.1 =
* Added Brazilian Portuguese translation. Thank you to Marcelo Vasconcelos Araújo for this contribution!

= 0.8 =
* Updated to newer version of the MCAPI library. Thanks to [David Cowgill](http://wordpress.org/support/profile/dcowgill) for pointing out that this was needed.

= 0.7.5 =
* Added notice to folks who don't have cURL installed, to cut down on some source of confusion about whether the plugin works or not.

= 0.7.2 =
* Adds Dutch language support. Thank you to Angelique Schäffer for this contribution!
* The French translation seems to have been missing. This actually adds it in again.

= 0.7.1 =
* Restores the original autoloader.¨

= 0.7 =
* Added French language support. Thank you to Frederick Marcoux for this contribution!
* Fixed bug affecting display of the settings page on subdirectory installs from the setup notice link. This should also improve the plugin for multisite installations. Thank you to [alex chousmith](http://wordpress.org/support/profile/chousmith) for bringing this to my attention.

= 0.6.2 =
* Removed old merge data. Thank you to [huguespisapia](http://wordpress.org/support/profile/huguespisapia) for letting me know about this.

= 0.6.1 =
* Cleaned up a few more WP_DEBUG mode errors.

= 0.6 =
* Cleaned up errors from WP_DEBUG mode.

= 0.5.2 =
* Fixed bug that prevented first and last name fields from both displaying.
* Fixed bug that hid error messages when MailChimp portion of the signup was unsuccessful.

= 0.5 =
* Updated fields to allow customization of success and failure notification messages.

= 0.3 =
* Added Danish translation of plugin.
* Fixed code for loading translations.

= 0.2.1 =
* Wrap spl_autoload in try catch to prevent fatal error on some systems.

= 0.2 =
* Adds optional first and last name fields.

= 0.1.3 =
* Began internationalizing the plugin.

= 0.1.2 =
* Fixed issue with widget button label.

= 0.1.1 =
* Updated link in admin notices.

= 0.1a =
* Changed filenames which were causing an issue with autoloading on some systems.

= 0.1 =
* First release.

== Upgrade Notice ==

= 1.0 =

* Initial Version.

== License ==

This file is part of Your Plugin Name.

MailChimp Widget is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

MailChimp Widget is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

Get a copy of the GNU General Public License in <http://www.gnu.org/licenses/>.