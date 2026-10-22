# Changelog

All notable changes to this project.
Format: [Keep a Changelog](https://keepachangelog.com/). Versioning: [SemVer](https://semver.org/).

## [1.2.1] - 2026-10-22

### Changed
- GitHub username corrected from the `mohsinjarral` placeholder to `Jarral05`
  in the plugin header and the update checker. With the placeholder in place
  the update check queried a repository that does not exist, and failed
  silently with no notice and no error.

## [1.2.0] - 2026-10-19

### Fixed
- SVG icons carried ~7.7 KB of embedded C2PA provenance metadata each, 65 KB
  across the eight files. `wp_kses()` stripped the tags at output so nothing
  was ever served to a visitor, but WordPress parsed 65 KB of dead weight on
  every page load. Icons regenerated clean: 65,153 bytes -> 2,835 bytes.

### Verified this release (PHP 8.3.6, executed not just inspected)
- All 35 project PHP files and all 129 bundled library files pass `php -l`.
- Theme loads and runs with no fatals; all hooks fire.
- Icon loader: 10 path-traversal payloads attempted, all 10 blocked.
- Both JSON-LD schema blocks emit valid JSON; FAQ schema count matches the
  rendered page exactly (7 = 7).
- Form handler attacked with honeypot, sub-3-second timing, missing nonce,
  malformed email, short and oversized messages, link spam, oversized name,
  forged dropdown values and stored XSS. Every guard held; nothing unwanted
  was written to the database.
- Rate limit fires on the 6th attempt against a cap of 5.
- Validation errors reach the user and submitted input survives the bounce.

## [1.1.0] - 2026-09-29

### Security
- `jd_icon()` now uses a strict `[a-z0-9-]` allowlist plus a `realpath()`
  containment check, rather than `sanitize_file_name()` alone. Closes a
  theoretical path-traversal surface in the icon loader.
- Added the `Update URI` header to the theme and both plugins. Without it,
  a plugin published on wordpress.org under the same folder name could be
  served to your sites as an "update".

### Added
- GitHub-based update checking (Plugin Update Checker 5.7, MIT).
- `uninstall.php` for both plugins. Enquiries and Projects are deliberately
  preserved; only transients and rewrite rules are cleared.
- `load_theme_textdomain()` and `index.php` silence files throughout.
- `readme.txt` for both plugins.

### Fixed
- `remove_action()` was called with four arguments; it accepts three.
- `wp_sprintf()` replaced with `sprintf()` where no list formatting was used.

## [1.0.0] - 2026-09-29

### Added
- First release: 25-file classic theme, Projects plugin, Forms plugin.
