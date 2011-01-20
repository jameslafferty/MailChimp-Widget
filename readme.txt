=== MailChimp Widget ===
Contributors: jameslafferty
Tags: newsletter, MailChimp, mailing list, widget, email marketing
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JSL4JTA4KMZLG
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: trunk

Adds a widget that allows your users to sign up for your MailChimp mailing list.

== Description ==

This plugin provides an easy, lightweight way to let your users sign up for your MailChimp list. You can use it to sign up users for 
several different lists by creating multiple instances of the widget. Once a user has signed up, a cookie is stored on their machine to
prevent the sign up form for that particular list from displaying. Sign ups for other lists will display.

The MailChimp Widget:

*	is easy to use
*	is AJAX-enabled, but degrades gracefully if Javascript isn't turned on
*	encourages the collection of only information that you actually need (i.e., an email address) to send your mailers

If you find this plugin useful, please rate it and/or make a donation.

== Installation ==
1. Upload the mailchimp_widget to /wp-content/plugins/.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Enter a valid MailChimp API key on the plugin admin page ("Settings" >> "MailChimp Widget"). You can obtain a MailChimp API key after creating an account at MailChimp.com.
1. Drag the widget into your sidebar from the "Widgets" menu in WordPress.
1. Select a mailing list and you're ready to go!
1. Please rate the plugin.

== Frequently Asked Questions ==
= I can't activate the plugin because it triggers this error: "Parse error: syntax error, unexpected '{' in .../wp-content/plugins/mailchimp-widget/mailchimp-widget.php on line 40." What's going on? =
Check your PHP version. You need at least PHP 5.1.2 to use this plugin.

== Screenshots ==
1. Just add your MailChimp API key.
2. Select your Widget Options.
3. The widget displays in your sidebar.

== Changelog ==
= 0.7 =
* Added French language support. Thank you to Frederick Marcoux for this contribution!

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
= 0.7 =
* Adds French language support.

= 0.6.2 =
* Removed old merge data. Thank you to [huguespisapia](http://wordpress.org/support/profile/huguespisapia) for letting me know about this. (critical upgrade)

= 0.6.1 =
* Corrects some additional minor errors that come up in WP_DEBUG mode.

= 0.6 =
* Cleaned up some minor errors from WP_DEBUG.

= 0.5.2 =
* Major bug fixes to display error message when sign up fails on the MailChimp side and to display both first and last name fields when set up.

= 0.5 =
* Customize visitor signup success and failure messages.

= 0.3 =
* Fixes a bug in translation code.
* Adds a Danish translation of the plugin.

= 0.2.1 =
* Bugfix to correct functioning of spl_autoload on some systems.

= 0.2 =
* Now have optional first and last name fields for the widget.

= 0.1.3 =
* Added .pot file and languages folder.

= 0.1.1 =
* Fixed link in admin notices to be actually functional.

= 0.1a =
* This may help if you're getting an error on activation.

= 0.1 =
* First release.

== Internationalization (i18n) ==
Currently, translations are included to the following languages:

* da_DK - Danish in Denmark. Thank you to [joynielsen](http://joyfulliving.dk) for contributing!
* fr_FR - French. Thank you to Frederick Marcoux for contributing!

If you're interested in doing a translation into your language, please let me know.