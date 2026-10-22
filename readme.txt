=== Jarral Projects ===
Contributors: Jarral05
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Projects post type and Service Type taxonomy for the portfolio grid.

== Description ==

Registers the Projects post type, the Service Type taxonomy, and two extra fields per project: client industry and live URL. Kept as a plugin so projects survive a theme change.

== Changelog ==

= 1.1.0 =
* Added Update URI header (prevents a same-slug wordpress.org plugin overwriting this one)
* Added GitHub update checking
* Added uninstall.php
* Hardened the icon loader with a strict allowlist and realpath containment
* Added translation loading

= 1.0.0 =
* First release
