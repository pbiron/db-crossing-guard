=== DB Crossing Guard ===

Contributors: pbiron
Tags: database, security, privacy
Requires at least: 6.9
Requires PHP: 8.3
Tested up to: 7.0
Stable tag: 0.2.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z6D97FA595WSU

Display various indicators to let admins know whether the DB connection is encrypted

== Description ==

To be written.

== Installation ==

From your WordPress dashboard

1. Go to _Plugins > Add New_ and click on _Upload Plugin_
2. Upload the zip file
3. Activate the plugin


== Screenshots ==

To be written.

== Frequently Asked Questions ==

To be written.

== Changelog ==

= 0.2.4 (2025-05-29) =

* Misc
    * Update composer dependencies for compatibility with PHP 8.4
    * Update tested up, etc plugin headers

= 0.2.3 (2024-11-22) =

* Enhancements
    * I18N: load translations on `after_setup_theme` to be compatible with WP 6.7+

= 0.2.2 (?) =

* Unknown

= 0.2.1 (2021-02-02) =

* Miscellaneous
    * Added GitHub URL to the plugin header

= 0.2.0 (2021-02-02) =

* Miscellaneous
    * Plugin renamed to DB Crossing Guard, slug to db-crossing-guard and Namespace to SHC\DB_CROSSING_GUARD

= 0.1.1 (2021-02-02) =

* Enhancements
    * Various documentation and WPCS improvements
    * Minor code refactoring
    * Admin Bar Node
        * When connection is encrypted, the encryption used is now displayed in a child node, instead of as @title hover text
    * At a Glance
        * Connection status now also appears in the Network Right Now widget (for multisite) 
    * Site Health
        * The test and debug descriptions have been slightly improved

= 0.1.0 (2021-01-30) =

* init commit.
